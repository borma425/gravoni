<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loss extends Model
{
    protected $fillable = [
        'product_id',
        'size',
        'color',
        'quantity',
        'cost_price_at_loss',
        'total_loss',
        'note',
        'stock_movement_id',
    ];

    protected $casts = [
        'cost_price_at_loss' => 'decimal:2',
        'total_loss' => 'decimal:2',
    ];

    /**
     * Get the product that owns this loss
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the stock movement associated with this loss
     */
    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class);
    }
}
