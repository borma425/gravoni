<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'tracking_id',
        'customer_name',
        'customer_address',
        'customer_numbers',
        'governorate_id',
        'delivery_fees',
        'items',
        'total_amount',
        'status',
        'payment_method',
        'shipping_data',
    ];

    protected $casts = [
        'customer_numbers' => 'array',
        'items' => 'array',
        'shipping_data' => 'array',
        'delivery_fees' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function governorate()
    {
        return $this->belongsTo(Governorate::class);
    }

    /**
     * Get status label in Arabic
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'accepted' => 'تم القبول',
            'delivery_fees_paid' => 'تم دفع رسوم التوصيل',
            'shipped' => 'تم الشحن',
            'cancelled' => 'مرفوض',
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
            'cod' => 'الدفع عند الاستلام',
            'cashup' => 'دفع رسوم التوصيل فقط',
            default => $this->payment_method ?? '-',
        };
    }

    /**
     * Generate unique tracking ID
     */
    public static function generateTrackingId(): string
    {
        do {
            $id = 'GRV' . strtoupper(substr(uniqid(), -6)) . rand(100, 999);
        } while (static::where('tracking_id', $id)->exists());
        return $id;
    }

    /**
     * المبلغ الإجمالي الفعلي: من total_amount أو من items + delivery_fees إذا كان 0
     */
    public function getEffectiveTotalAmountAttribute(): float
    {
        $stored = (float) ($this->attributes['total_amount'] ?? 0);
        if ($stored > 0) {
            return $stored;
        }
        return $this->items_revenue + (float) ($this->delivery_fees ?? 0);
    }

    /**
     * إيرادات المنتجات فقط (بدون رسوم الشحن) - تُستخدم في حساب إجمالي الإيرادات
     */
    public function getItemsRevenueAttribute(): float
    {
        return collect($this->items ?? [])->sum(
            fn ($i) => ((float) ($i['price'] ?? 0)) * ((int) ($i['quantity'] ?? 0))
        );
    }
}
