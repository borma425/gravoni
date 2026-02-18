<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'selling_price',
        'discounted_price',
        'quantity',
        'description',
        'available_sizes',
        'available_colors',
        'sample',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'quantity' => 'integer',
        'available_sizes' => 'array',
        'available_colors' => 'array',
    ];

    /**
     * Get all purchases for this product
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get all sales for this product
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get all stock movements for this product
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get all losses for this product
     */
    public function losses(): HasMany
    {
        return $this->hasMany(\App\Models\Loss::class);
    }

    /**
     * Get current stock quantity (from products table)
     */
    public function getCurrentStockAttribute(): int
    {
        return $this->quantity ?? 0;
    }

    /**
     * Calculate average cost price
     */
    public function getAverageCostPriceAttribute(): float
    {
        $totalValue = 0;
        $totalQuantity = 0;

        // Get all purchase movements
        $purchases = $this->stockMovements()
            ->where('type', 'purchase')
            ->orWhere('type', 'purchase_return')
            ->get();

        foreach ($purchases as $movement) {
            if ($movement->type === 'purchase') {
                $purchase = Purchase::find($movement->reference_id);
                if ($purchase) {
                    $totalValue += $purchase->cost_price * $movement->quantity;
                    $totalQuantity += $movement->quantity;
                }
            } elseif ($movement->type === 'purchase_return') {
                // Subtract returned purchases
                $purchase = Purchase::find($movement->reference_id);
                if ($purchase) {
                    $totalValue -= $purchase->cost_price * abs($movement->quantity);
                    $totalQuantity += $movement->quantity; // quantity is negative
                }
            }
        }

        if ($totalQuantity <= 0) {
            return 0;
        }

        return $totalValue / $totalQuantity;
    }
}
