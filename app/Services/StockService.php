<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Calculate average cost price for a product
     */
    public function calculateAverageCost(Product $product): float
    {
        $totalValue = 0;
        $totalQuantity = 0;

        // Get all purchase movements (positive quantities)
        $purchaseMovements = $product->stockMovements()
            ->whereIn('type', ['purchase', 'purchase_return'])
            ->get();

        foreach ($purchaseMovements as $movement) {
            $purchase = Purchase::find($movement->reference_id);
            if ($purchase) {
                if ($movement->type === 'purchase') {
                    $totalValue += $purchase->cost_price * $movement->quantity;
                    $totalQuantity += $movement->quantity;
                } elseif ($movement->type === 'purchase_return') {
                    // Subtract returned purchases (quantity is negative)
                    $totalValue -= $purchase->cost_price * abs($movement->quantity);
                    $totalQuantity += $movement->quantity; // quantity is already negative
                }
            }
        }

        if ($totalQuantity <= 0) {
            return 0;
        }

        return round($totalValue / $totalQuantity, 2);
    }

    /**
     * Get current stock quantity (from available_sizes or legacy quantity)
     */
    public function getCurrentStock(Product $product): int
    {
        return $product->total_stock;
    }

    /**
     * Update product stock (both generic quantity and specific size/color)
     */
    public function updateProductStock(Product $product, int $quantityChange, ?string $size = null, ?string $color = null): void
    {
        if ($size && $color) {
            $sizes = $product->available_sizes ?? [];
            $foundType = false;
            foreach ($sizes as &$sData) {
                if (($sData['size'] ?? '') === $size) {
                    $colors = $sData['colors'] ?? [];
                    foreach ($colors as &$cData) {
                        if (($cData['color'] ?? '') === $color) {
                            $cData['stock'] = max(0, ((int)($cData['stock'] ?? 0)) + $quantityChange);
                            $foundType = true;
                            break;
                        }
                    }
                    $sData['colors'] = $colors;
                    if ($foundType) break;
                }
            }
            if ($foundType) {
                $product->available_sizes = $sizes;
            }

            // Recalculate generic quantity as sum of all colors in all sizes
            $totalQuantity = 0;
            foreach ($product->available_sizes ?? [] as $s) {
                foreach ($s['colors'] ?? [] as $c) {
                    $totalQuantity += (int)($c['stock'] ?? 0);
                }
            }
            $product->quantity = $totalQuantity;
        } else {
            // Non-specific update (legacy fallback)
            $product->quantity = max(0, $product->quantity + $quantityChange);
        }
        
        $product->save();
    }

    /**
     * Record a purchase
     */
    public function recordPurchase(Product $product, int $quantity, float $costPrice, ?string $size = null, ?string $color = null): Purchase
    {
        return DB::transaction(function () use ($product, $quantity, $costPrice, $size, $color) {
            // Create purchase record
            $purchase = Purchase::create([
                'product_id' => $product->id,
                'size' => $size,
                'color' => $color,
                'quantity' => $quantity,
                'cost_price' => $costPrice,
            ]);

            // Update product quantity and size/color stock
            $this->updateProductStock($product, $quantity, $size, $color);

            $noteDetails = [];
            if ($size) $noteDetails[] = "المقاس: {$size}";
            if ($color) $noteDetails[] = "اللون: {$color}";
            $noteSuffix = empty($noteDetails) ? "" : " (" . implode(' - ', $noteDetails) . ")";

            // Create stock movement
            StockMovement::create([
                'product_id' => $product->id,
                'size' => $size,
                'color' => $color,
                'type' => StockMovement::TYPE_PURCHASE,
                'quantity' => $quantity,
                'reference_id' => $purchase->id,
                'note' => "شراء {$quantity} وحدة بسعر {$costPrice}{$noteSuffix}",
            ]);

            return $purchase;
        });
    }

    /**
     * Record a sale
     */
    public function recordSale(Product $product, int $quantity, float $sellingPrice, string $governorate = null, ?string $size = null, ?string $color = null): Sale
    {
        return DB::transaction(function () use ($product, $quantity, $sellingPrice, $governorate, $size, $color) {
            // Check if stock is sufficient
            // If strict size/color, we should check that specific bucket, but for now we rely on the generic stock or frontend validation.
            $currentStock = $this->getCurrentStock($product);
            if ($currentStock < $quantity) {
                throw new \Exception("المخزون غير كافي. المخزون الكلي الحالي: {$currentStock}");
            }

            // Get average cost price
            $averageCost = $this->calculateAverageCost($product);

            // Calculate profit
            $profit = ($sellingPrice - $averageCost) * $quantity;

            // Create sale record
            $sale = Sale::create([
                'product_id' => $product->id,
                'size' => $size,
                'color' => $color,
                'quantity' => $quantity,
                'selling_price' => $sellingPrice,
                'cost_price_at_sale' => $averageCost,
                'profit' => $profit,
                'governorate' => $governorate,
            ]);

            // Update product quantity
            $this->updateProductStock($product, -$quantity, $size, $color);

            $noteDetails = [];
            if ($size) $noteDetails[] = "المقاس: {$size}";
            if ($color) $noteDetails[] = "اللون: {$color}";
            $noteSuffix = empty($noteDetails) ? "" : " (" . implode(' - ', $noteDetails) . ")";

            // Create stock movement (negative quantity)
            StockMovement::create([
                'product_id' => $product->id,
                'size' => $size,
                'color' => $color,
                'type' => StockMovement::TYPE_SALE,
                'quantity' => -$quantity,
                'reference_id' => $sale->id,
                'note' => "بيع {$quantity} وحدة بسعر {$sellingPrice}{$noteSuffix}",
            ]);

            return $sale;
        });
    }

    /**
     * Record a sales return
     */
    public function recordSalesReturn(Sale $sale, int $quantity): StockMovement
    {
        return DB::transaction(function () use ($sale, $quantity) {
            // Get already returned quantity
            $alreadyReturned = $sale->returns()->sum('quantity');
            $remainingQuantity = $sale->quantity - $alreadyReturned;
            
            if ($quantity > $remainingQuantity) {
                throw new \Exception("الكمية المرتجعة ({$quantity}) أكبر من الكمية المتبقية ({$remainingQuantity})");
            }

            // Update product quantity and size stock
            $product = Product::find($sale->product_id);
            $this->updateProductStock($product, $quantity, $sale->size, $sale->color);

            $noteDetails = [];
            if ($sale->size) $noteDetails[] = "المقاس: {$sale->size}";
            if ($sale->color) $noteDetails[] = "اللون: {$sale->color}";
            $noteSuffix = empty($noteDetails) ? "" : " (" . implode(' - ', $noteDetails) . ")";

            // Create stock movement (positive quantity to add back to stock)
            $movement = StockMovement::create([
                'product_id' => $sale->product_id,
                'size' => $sale->size,
                'color' => $sale->color,
                'type' => StockMovement::TYPE_SALES_RETURN,
                'quantity' => $quantity,
                'reference_id' => $sale->id,
                'note' => "مرتجع بيع {$quantity} وحدة{$noteSuffix}",
            ]);

            // Update sale profit (subtract returned profit)
            $returnedProfit = ($sale->selling_price - $sale->cost_price_at_sale) * $quantity;
            $sale->profit -= $returnedProfit;
            $sale->save();

            return $movement;
        });
    }

    /**
     * Record a purchase return
     */
    public function recordPurchaseReturn(Purchase $purchase, int $quantity): StockMovement
    {
        return DB::transaction(function () use ($purchase, $quantity) {
            // Get already returned quantity
            $alreadyReturned = abs($purchase->returns()->sum('quantity'));
            $remainingQuantity = $purchase->quantity - $alreadyReturned;
            
            if ($quantity > $remainingQuantity) {
                throw new \Exception("الكمية المرتجعة ({$quantity}) أكبر من الكمية المتبقية ({$remainingQuantity})");
            }

            // Update product quantity and size stock
            $product = Product::find($purchase->product_id);
            $this->updateProductStock($product, -$quantity, $purchase->size, $purchase->color);

            $noteDetails = [];
            if ($purchase->size) $noteDetails[] = "المقاس: {$purchase->size}";
            if ($purchase->color) $noteDetails[] = "اللون: {$purchase->color}";
            $noteSuffix = empty($noteDetails) ? "" : " (" . implode(' - ', $noteDetails) . ")";

            // Create stock movement (negative quantity to subtract from stock)
            $movement = StockMovement::create([
                'product_id' => $purchase->product_id,
                'size' => $purchase->size,
                'color' => $purchase->color,
                'type' => StockMovement::TYPE_PURCHASE_RETURN,
                'quantity' => -$quantity,
                'reference_id' => $purchase->id,
                'note' => "مرتجع شراء {$quantity} وحدة{$noteSuffix}",
            ]);

            return $movement;
        });
    }

    /**
     * Record damage
     */
    public function recordDamage(Product $product, int $quantity, string $note = null, ?string $size = null, ?string $color = null): array
    {
        return DB::transaction(function () use ($product, $quantity, $note, $size, $color) {
            $currentStock = $this->getCurrentStock($product);
            if ($currentStock < $quantity) {
                throw new \Exception("المخزون غير كافي. المخزون الحالي: {$currentStock}");
            }

            // Get average cost price
            $averageCost = $this->calculateAverageCost($product);
            
            // Calculate total loss
            $totalLoss = $averageCost * $quantity;

            // Update product quantity and size stock
            $this->updateProductStock($product, -$quantity, $size, $color);

            $noteDetails = [];
            if ($size) $noteDetails[] = "المقاس: {$size}";
            if ($color) $noteDetails[] = "اللون: {$color}";
            $noteSuffix = empty($noteDetails) ? "" : " (" . implode(' - ', $noteDetails) . ")";

            // Create stock movement (negative quantity)
            $movement = StockMovement::create([
                'product_id' => $product->id,
                'size' => $size,
                'color' => $color,
                'type' => StockMovement::TYPE_DAMAGE,
                'quantity' => -$quantity,
                'reference_id' => null,
                'note' => ($note ?? "تلف {$quantity} وحدة") . $noteSuffix,
            ]);

            // Create loss record
            $loss = \App\Models\Loss::create([
                'product_id' => $product->id,
                'size' => $size,
                'color' => $color,
                'quantity' => $quantity,
                'cost_price_at_loss' => $averageCost,
                'total_loss' => $totalLoss,
                'note' => $note,
                'stock_movement_id' => $movement->id,
            ]);

            return ['movement' => $movement, 'loss' => $loss];
        });
    }

}

