<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reference_id',
        'note',
    ];

    const TYPE_PURCHASE = 'purchase';
    const TYPE_SALE = 'sale';
    const TYPE_SALES_RETURN = 'sales_return';
    const TYPE_PURCHASE_RETURN = 'purchase_return';
    const TYPE_DAMAGE = 'damage';
    const TYPE_ADJUSTMENT = 'adjustment';

    /**
     * Get the product that owns this stock movement
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the loss associated with this stock movement (if damage type)
     */
    public function loss()
    {
        return $this->hasOne(\App\Models\Loss::class, 'stock_movement_id');
    }
}
