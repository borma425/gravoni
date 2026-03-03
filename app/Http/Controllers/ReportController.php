<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SalesStatsService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Get date range from period type
     */
    private function getDateRangeFromPeriod(?string $period): array
    {
        $today = Carbon::today();
        return match ($period) {
            'today' => [$today, $today],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()],
            'current_month' => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
            'previous_month' => [$today->copy()->subMonth()->startOfMonth(), $today->copy()->subMonth()->endOfMonth()],
            'current_year' => [$today->copy()->startOfYear(), $today->copy()->endOfYear()],
            default => [null, null],
        };
    }

    /**
     * Show profit report
     */
    public function profit(Request $request)
    {
        $period = $request->get('period', 'current_month');
        $customFrom = $request->get('from_date');
        $customTo = $request->get('to_date');

        $from = null;
        $to = null;

        if ($period === 'custom' && $customFrom && $customTo) {
            $from = Carbon::parse($customFrom)->startOfDay();
            $to = Carbon::parse($customTo)->endOfDay();
        } elseif ($period !== 'custom') {
            [$from, $to] = $this->getDateRangeFromPeriod($period);
        }

        // إحصائيات موحدة (مبيعات يدوية + أوردرات) عبر SalesStatsService
        $totalSales = SalesStatsService::salesCountBetween($from, $to);
        $totalRevenue = SalesStatsService::revenueBetween($from, $to);
        $grossProfit = SalesStatsService::profitBetween($from, $to);
        $expenses = Expense::sumBetween($from, $to);
        $netProfit = $grossProfit - $expenses;

        // قائمة موحدة: مبيعات يدوية + بنود الأوردرات
        $manualSales = Sale::with('product')
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to->toDateString()))
            ->latest()
            ->get();

        $orders = Order::whereIn('status', SalesStatsService::SOLD_ORDER_STATUSES)
            ->when($from, fn ($q) => $q->whereDate('created_at', '>=', $from->toDateString()))
            ->when($to, fn ($q) => $q->whereDate('created_at', '<=', $to->toDateString()))
            ->latest()
            ->get();

        $productIds = $orders->flatMap(fn ($o) => collect($o->items ?? [])->pluck('product_id'))->unique()->filter()->map(fn ($id) => (int) $id);
        $products = $productIds->isNotEmpty() ? Product::whereIn('id', $productIds)->get()->keyBy('id') : collect();

        $rows = collect();
        foreach ($manualSales as $s) {
            $rows->push((object)[
                'product_name' => $s->product?->name ?? '-',
                'quantity' => $s->quantity,
                'selling_price' => $s->selling_price,
                'cost_price' => $s->cost_price_at_sale,
                'profit' => $s->profit,
                'date' => $s->created_at,
                'type' => 'manual',
            ]);
        }
        foreach ($orders as $order) {
            foreach ($order->items ?? [] as $item) {
                $pid = $item['product_id'] ?? null;
                $prod = $pid ? ($products[$pid] ?? null) : null;
                $cost = $prod ? (float) ($prod->average_cost_price ?? 0) : 0;
                $price = (float) ($item['price'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 0);
                $rows->push((object)[
                    'product_name' => $prod?->name ?? ('منتج #' . ($pid ?? '?')),
                    'quantity' => $qty,
                    'selling_price' => $price,
                    'cost_price' => $cost,
                    'profit' => ($price - $cost) * $qty,
                    'date' => $order->created_at,
                    'type' => 'order',
                    'tracking_id' => $order->tracking_id,
                ]);
            }
        }
        $rows = $rows->sortByDesc('date')->values();
        $reportRows = new \Illuminate\Pagination\LengthAwarePaginator(
            $rows->forPage(request('page', 1), 50)->values(),
            $rows->count(),
            50,
            request('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $periodLabel = $this->getPeriodLabel($period, $from, $to, $customFrom, $customTo);

        return view('reports.profit', compact(
            'reportRows',
            'totalSales',
            'totalRevenue',
            'grossProfit',
            'expenses',
            'netProfit',
            'period',
            'periodLabel',
            'customFrom',
            'customTo'
        ));
    }

    private function getPeriodLabel(?string $period, ?Carbon $from, ?Carbon $to, ?string $customFrom, ?string $customTo): string
    {
        if ($period === 'custom' && $customFrom && $customTo) {
            return "من {$customFrom} إلى {$customTo}";
        }
        return match ($period) {
            'today' => 'اليوم (' . Carbon::today()->format('Y-m-d') . ')',
            'yesterday' => 'أمس (' . Carbon::yesterday()->format('Y-m-d') . ')',
            'current_month' => 'الشهر الحالي (' . Carbon::now()->translatedFormat('F Y') . ')',
            'previous_month' => 'الشهر السابق (' . Carbon::now()->subMonth()->translatedFormat('F Y') . ')',
            'current_year' => 'السنة الحالية (' . Carbon::now()->year . ')',
            default => 'كل الفترات',
        };
    }

    /**
     * Show low stock products (based on total_stock from available_sizes or quantity)
     */
    public function lowStock()
    {
        $all = Product::all()->filter(fn ($p) => $p->total_stock < 10)->sortBy('total_stock')->values();
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $all->forPage(request('page', 1), 20)->values(),
            $all->count(),
            20,
            request('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('reports.low-stock', compact('products'));
    }

}
