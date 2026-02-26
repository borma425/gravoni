<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;

/**
 * Unified sales statistics: includes both manual sales (Sale) and accepted orders (Order).
 */
class SalesStatsService
{
    public const SOLD_ORDER_STATUSES = ['accepted', 'delivery_fees_paid', 'shipped'];

    public static function totalSalesCount(): int
    {
        $manualCount = Sale::count();
        $ordersCount = Order::whereIn('status', self::SOLD_ORDER_STATUSES)->count();
        return $manualCount + $ordersCount;
    }

    public static function totalRevenue(): float
    {
        $manualRevenue = Sale::get()->sum(fn ($s) => (float) $s->selling_price * (int) $s->quantity);
        $ordersRevenue = Order::whereIn('status', self::SOLD_ORDER_STATUSES)->get()->sum(fn ($o) => $o->items_revenue);
        return $manualRevenue + $ordersRevenue;
    }

    public static function totalProfit(): float
    {
        $manualProfit = (float) Sale::sum('profit');

        $orders = Order::whereIn('status', self::SOLD_ORDER_STATUSES)->get();
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
}
