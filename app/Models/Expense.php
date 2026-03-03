<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'amount',
        'expense_date',
        'category',
        'description',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Total expenses within date range (inclusive).
     */
    public static function sumBetween(?Carbon $from, ?Carbon $to): float
    {
        $query = static::query();
        if ($from) {
            $query->whereDate('expense_date', '>=', $from->toDateString());
        }
        if ($to) {
            $query->whereDate('expense_date', '<=', $to->toDateString());
        }
        return (float) $query->sum('amount');
    }

    /**
     * Common expense categories (افضل الممارسات: تصنيف المصاريف التشغيلية)
     */
    public static function categories(): array
    {
        return [
            'advertising' => 'إعلانات وتسويق',
            'shipping' => 'شحن وتوصيل',
            'rent' => 'إيجار',
            'salaries' => 'رواتب',
            'utilities' => 'كهرباء ومياه وإنترنت',
            'supplies' => 'مستلزمات مكتبية',
            'packaging' => 'تغليف وتعبئة',
            'maintenance' => 'صيانة',
            'other' => 'أخرى',
        ];
    }
}
