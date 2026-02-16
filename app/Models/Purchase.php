<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Purchase extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'cost_price',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
    ];

    /**
     * Get the product that owns this purchase
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get stock movements for this purchase (returns)
     */
    public function returns()
    {
        return $this->hasMany(\App\Models\StockMovement::class, 'reference_id')
            ->where('type', \App\Models\StockMovement::TYPE_PURCHASE_RETURN);
    }

    /**
     * Get total returned quantity
     */
    public function getReturnedQuantityAttribute(): int
    {
        return abs($this->returns()->sum('quantity'));
    }
}
