<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;

/**
 * Unified sales statistics: includes both manual sales (Sale) and accepted orders (Order).
 * Supports date-filtered stats for precise period reporting.
 */
class SalesStatsService
{
    public const SOLD_ORDER_STATUSES = ['accepted', 'delivery_fees_paid', 'shipped'];

    public static function totalSalesCount(): int
    {
        return self::salesCountBetween(null, null);
    }

    public static function totalRevenue(): float
    {
        return self::revenueBetween(null, null);
    }

    public static function totalProfit(): float
    {
        return self::profitBetween(null, null);
    }

    /**
     * Sales count within date range (inclusive). Null = no filter.
     */
    public static function salesCountBetween(?Carbon $from, ?Carbon $to): int
    {
        $manualQuery = Sale::query();
        $ordersQuery = Order::whereIn('status', self::SOLD_ORDER_STATUSES);

        if ($from) {
            $manualQuery->whereDate('created_at', '>=', $from->toDateString());
            $ordersQuery->whereDate('created_at', '>=', $from->toDateString());
        }
        if ($to) {
            $manualQuery->whereDate('created_at', '<=', $to->toDateString());
            $ordersQuery->whereDate('created_at', '<=', $to->toDateString());
        }

        return $manualQuery->count() + $ordersQuery->count();
    }

    /**
     * Revenue within date range (inclusive). Null = no filter.
     */
    public static function revenueBetween(?Carbon $from, ?Carbon $to): float
    {
        $manualQuery = Sale::query();
        $ordersQuery = Order::whereIn('status', self::SOLD_ORDER_STATUSES);

        if ($from) {
            $manualQuery->whereDate('created_at', '>=', $from->toDateString());
            $ordersQuery->whereDate('created_at', '>=', $from->toDateString());
        }
        if ($to) {
            $manualQuery->whereDate('created_at', '<=', $to->toDateString());
            $ordersQuery->whereDate('created_at', '<=', $to->toDateString());
        }

        $manualRevenue = $manualQuery->get()->sum(fn ($s) => (float) $s->selling_price * (int) $s->quantity);
        $ordersRevenue = $ordersQuery->get()->sum(fn ($o) => $o->items_revenue);

        return $manualRevenue + $ordersRevenue;
    }

    /**
     * Gross profit within date range (inclusive). Null = no filter.
     */
    public static function profitBetween(?Carbon $from, ?Carbon $to): float
    {
        $manualQuery = Sale::query();
        $ordersQuery = Order::whereIn('status', self::SOLD_ORDER_STATUSES);

        if ($from) {
            $manualQuery->whereDate('created_at', '>=', $from->toDateString());
            $ordersQuery->whereDate('created_at', '>=', $from->toDateString());
        }
        if ($to) {
            $manualQuery->whereDate('created_at', '<=', $to->toDateString());
            $ordersQuery->whereDate('created_at', '<=', $to->toDateString());
        }

        $manualProfit = (float) $manualQuery->sum('profit');
        $orders = $ordersQuery->get();
        $productIds = $orders->flatMap(fn ($o) => collect($o->items ?? [])->pluck('product_id'))->unique()->filter()->map(fn ($id) => (int) $id);
        $products = $productIds->isNotEmpty()
            ? Product::whereIn('id', $productIds)->get()->keyBy('id')
            : collect();

        $ordersProfit = 0;
        foreach ($orders as $order) {
            foreach ($order->items ?? [] as $item) {
                $pid = $item['product_id'] ?? null;
                $prod = $pid ? ($products[$pid] ?? null) : null;
                $cost = $prod ? (float) ($prod->average_cost_price ?? 0) : 0;
                $price = (float) ($item['price'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 0);
                $ordersProfit += ($price - $cost) * $qty;
            }
        }

        return $manualProfit + $ordersProfit;
    }

    /** اليوم */
    public static function forToday(): array
    {
        $today = Carbon::today();
        return [
            'sales_count' => self::salesCountBetween($today, $today),
            'revenue' => self::revenueBetween($today, $today),
            'profit' => self::profitBetween($today, $today),
        ];
    }

    /** أمس */
    public static function forYesterday(): array
    {
        $yesterday = Carbon::yesterday();
        return [
            'sales_count' => self::salesCountBetween($yesterday, $yesterday),
            'revenue' => self::revenueBetween($yesterday, $yesterday),
            'profit' => self::profitBetween($yesterday, $yesterday),
        ];
    }

    /** الشهر الحالي */
    public static function forCurrentMonth(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        return [
            'sales_count' => self::salesCountBetween($start, $end),
            'revenue' => self::revenueBetween($start, $end),
            'profit' => self::profitBetween($start, $end),
        ];
    }

    /** الشهر السابق */
    public static function forPreviousMonth(): array
    {
        $start = Carbon::now()->subMonth()->startOfMonth();
        $end = Carbon::now()->subMonth()->endOfMonth();
        return [
            'sales_count' => self::salesCountBetween($start, $end),
            'revenue' => self::revenueBetween($start, $end),
            'profit' => self::profitBetween($start, $end),
        ];
    }

    /** السنوي (السنة الحالية) */
    public static function forCurrentYear(): array
    {
        $start = Carbon::now()->startOfYear();
        $end = Carbon::now()->endOfYear();
        return [
            'sales_count' => self::salesCountBetween($start, $end),
            'revenue' => self::revenueBetween($start, $end),
            'profit' => self::profitBetween($start, $end),
        ];
    }
}
