<?php

namespace App\Http\Controllers;

use App\Http\Requests\DamageRequest;
use App\Http\Requests\PurchaseReturnRequest;
use App\Http\Requests\SalesReturnRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Show form for sales return (مبيعات يدوية + أوردرات الموقع)
     */
    public function showSalesReturnForm()
    {
        $returnables = collect();

        // مبيعات يدوية
        foreach (Sale::with('product')->latest()->get() as $sale) {
            $returned = abs($sale->returns()->sum('quantity'));
            $maxQty = max(0, $sale->quantity - $returned);
            if ($maxQty > 0 && $sale->product) {
                $returnables->put('sale-' . $sale->id, [
                    'type' => 'sale',
                    'label' => $sale->product->name . ($sale->size ? ' - ' . $sale->size : '') . ($sale->color ? ' - ' . $sale->color : ''),
                    'max_qty' => $maxQty,
                    'date' => $sale->created_at->format('Y-m-d'),
                ]);
            }
        }

        // أوردرات الموقع (مقبولة / شحن / مدفوع توصيل)
        $orders = \App\Models\Order::whereIn('status', ['accepted', 'delivery_fees_paid', 'shipped'])
            ->latest()
            ->get();
        foreach ($orders as $order) {
            $items = $order->items ?? [];
            $returnedByItem = $this->getOrderItemReturnedQuantities($order->id);
            foreach ($items as $idx => $item) {
                $pid = $item['product_id'] ?? null;
                $qty = (int) ($item['quantity'] ?? 0);
                if (!$pid || $qty <= 0) continue;
                $returned = $returnedByItem[$idx] ?? 0;
                $maxQty = max(0, $qty - $returned);
                if ($maxQty <= 0) continue;
                $product = \App\Models\Product::find($pid);
                $name = $product ? $product->name : ($item['product_name'] ?? 'منتج #' . $pid);
                $key = 'order-' . $order->id . '-' . $idx;
                $returnables->put($key, [
                    'type' => 'order',
                    'label' => $name . ($item['size'] ?? '') . ($item['color'] ?? ''),
                    'max_qty' => $maxQty,
                    'date' => $order->created_at->format('Y-m-d'),
                ]);
            }
        }

        return view('stock-movements.sales-return', compact('returnables'));
    }

    private function getOrderItemReturnedQuantities(int $orderId): array
    {
        $movements = \App\Models\StockMovement::where('type', 'order_return')
            ->where('reference_id', $orderId)
            ->get();
        $result = [];
        foreach ($movements as $m) {
            $idx = (int) ($m->order_item_index ?? 0);
            $result[$idx] = ($result[$idx] ?? 0) + abs($m->quantity);
        }
        return $result;
    }

    /**
     * Record sales return (مبيع أو أوردر)
     */
    public function recordSalesReturn(SalesReturnRequest $request)
    {
        $key = $request->returnable_key;
        $quantity = (int) $request->quantity;

        try {
            if (str_starts_with($key, 'sale-')) {
                $saleId = (int) substr($key, 5);
                $sale = Sale::findOrFail($saleId);
                $this->stockService->recordSalesReturn($sale, $quantity);
                return redirect()->route('stock-movements.sales-return')
                    ->with('success', 'تم تسجيل مرتجع البيع بنجاح');
            }
            if (str_starts_with($key, 'order-')) {
                $parts = explode('-', $key);
                $orderId = (int) ($parts[1] ?? 0);
                $itemIndex = (int) ($parts[2] ?? 0);
                $this->stockService->recordOrderItemReturn($orderId, $itemIndex, $quantity);
                return redirect()->route('stock-movements.sales-return')
                    ->with('success', 'تم تسجيل مرتجع الأوردر بنجاح');
            }
            throw new \Exception('مصدر غير صالح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show form for purchase return
     */
    public function showPurchaseReturnForm()
    {
        $purchases = Purchase::with('product')->latest()->get();

        return view('stock-movements.purchase-return', compact('purchases'));
    }

    /**
     * Record purchase return
     */
    public function recordPurchaseReturn(PurchaseReturnRequest $request)
    {
        $purchase = Purchase::findOrFail($request->purchase_id);

        try {
            $this->stockService->recordPurchaseReturn($purchase, $request->quantity);

            return redirect()->route('stock-movements.purchase-return')
                ->with('success', 'تم تسجيل مرتجع الشراء بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show form for damage
     */
    public function showDamageForm()
    {
        $products = Product::all()->map(function ($p) {
            $p->average_cost = $this->stockService->calculateAverageCost($p);
            return $p;
        });

        return view('stock-movements.damage', compact('products'));
    }

    /**
     * Record damage
     */
    public function recordDamage(DamageRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $costPrice = $request->filled('cost_price_at_loss') ? (float) $request->cost_price_at_loss : null;

        try {
            $result = $this->stockService->recordDamage($product, $request->quantity, $request->note, $request->size, $request->color, $costPrice);

            return redirect()->route('losses.index')
                ->with('success', 'تم تسجيل التلف والخسارة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display a listing of damages
     */
    public function indexDamages()
    {
        $damages = \App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_DAMAGE)
            ->with('product')
            ->latest()
            ->paginate(20);

        return view('stock-movements.damages-index', compact('damages'));
    }

    public function destroyDamage(\App\Models\StockMovement $movement)
    {
        try {
            // Find associated loss
            $loss = \App\Models\Loss::where('stock_movement_id', $movement->id)->first();
            
            if ($loss) {
                // Restore product quantity
                $product = $loss->product;
                $this->stockService->updateProductStock($product, abs($movement->quantity), $loss->size, $loss->color);
                
                // Delete loss
                $loss->delete();
            } else {
                // If no loss record, just restore quantity
                $product = $movement->product;
                $this->stockService->updateProductStock($product, abs($movement->quantity), $movement->size, $movement->color);
            }

            $movement->delete();

            return redirect()->route('stock-movements.damage.index')
                ->with('success', 'تم حذف التلف بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }

}
