<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_address',
        'customer_numbers',
        'delivery_fees',
        'items',
        'total_amount',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'customer_numbers' => 'array',
        'items' => 'array',
        'delivery_fees' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get status label in Arabic
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'delivery_fees_paid' => 'تم دفع رسوم التوصيل',
            'shipped' => 'تم الشحن',
            default => $this->status,
        };
    }

    /**
     * Get payment method label in Arabic
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'InstaPay' => 'InstaPay',
            'wallet' => 'محفظة',
            default => '-',
        };
    }
}
