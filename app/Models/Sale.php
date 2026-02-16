<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'selling_price',
        'cost_price_at_sale',
        'profit',
        'governorate',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'cost_price_at_sale' => 'decimal:2',
        'profit' => 'decimal:2',
    ];

    /**
     * Get the product that owns this sale
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get stock movements for this sale (returns)
     */
    public function returns()
    {
        return $this->hasMany(\App\Models\StockMovement::class, 'reference_id')
            ->where('type', \App\Models\StockMovement::TYPE_SALES_RETURN);
    }

    /**
     * Get total returned quantity
     */
    public function getReturnedQuantityAttribute(): int
    {
        return $this->returns()->sum('quantity');
    }
}
