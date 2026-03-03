<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SalesStatsService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display the dashboard
     */
    public function index()
    {
        $totalProducts = Product::count();
        $totalSales = SalesStatsService::totalSalesCount();
        $totalProfit = SalesStatsService::totalProfit();
        $totalRevenue = SalesStatsService::totalRevenue();
        
        // Get low stock products (less than 10) - based on total_stock from available_sizes or quantity
        $lowStockProducts = Product::all()->filter(fn ($p) => $p->total_stock < 10)->sortBy('total_stock')->take(10)->values();

        // Get recent damages
        $recentDamages = \App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_DAMAGE)
            ->with('product')
            ->latest()
            ->limit(5)
            ->get();

        // Calculate total damaged quantity
        $totalDamaged = \App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_DAMAGE)
            ->sum('quantity');

        // Get total losses
        $totalLosses = \App\Models\Loss::sum('total_loss');
        $recentLosses = \App\Models\Loss::with('product')->latest()->limit(5)->get();

        // إحصائيات لفترات محددة (اليوم، أمس، الشهر الحالي، الشهر السابق، السنوي)
        $today = SalesStatsService::forToday();
        $yesterday = SalesStatsService::forYesterday();
        $currentMonth = SalesStatsService::forCurrentMonth();
        $previousMonth = SalesStatsService::forPreviousMonth();
        $currentYear = SalesStatsService::forCurrentYear();

        // المصاريف لكل فترة (تُخصم من الأرباح)
        $expensesToday = Expense::sumBetween(Carbon::today(), Carbon::today());
        $expensesYesterday = Expense::sumBetween(Carbon::yesterday(), Carbon::yesterday());
        $expensesCurrentMonth = Expense::sumBetween(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $expensesPreviousMonth = Expense::sumBetween(Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth());
        $expensesCurrentYear = Expense::sumBetween(Carbon::now()->startOfYear(), Carbon::now()->endOfYear());

        $periodStats = [
            'today' => [
                'label' => 'اليوم (' . Carbon::today()->format('Y-m-d') . ')',
                'sales_count' => $today['sales_count'],
                'revenue' => $today['revenue'],
                'gross_profit' => $today['profit'],
                'expenses' => $expensesToday,
                'net_profit' => $today['profit'] - $expensesToday,
            ],
            'yesterday' => [
                'label' => 'أمس (' . Carbon::yesterday()->format('Y-m-d') . ')',
                'sales_count' => $yesterday['sales_count'],
                'revenue' => $yesterday['revenue'],
                'gross_profit' => $yesterday['profit'],
                'expenses' => $expensesYesterday,
                'net_profit' => $yesterday['profit'] - $expensesYesterday,
            ],
            'current_month' => [
                'label' => 'الشهر الحالي (' . Carbon::now()->translatedFormat('F Y') . ')',
                'sales_count' => $currentMonth['sales_count'],
                'revenue' => $currentMonth['revenue'],
                'gross_profit' => $currentMonth['profit'],
                'expenses' => $expensesCurrentMonth,
                'net_profit' => $currentMonth['profit'] - $expensesCurrentMonth,
            ],
            'previous_month' => [
                'label' => 'الشهر السابق (' . Carbon::now()->subMonth()->translatedFormat('F Y') . ')',
                'sales_count' => $previousMonth['sales_count'],
                'revenue' => $previousMonth['revenue'],
                'gross_profit' => $previousMonth['profit'],
                'expenses' => $expensesPreviousMonth,
                'net_profit' => $previousMonth['profit'] - $expensesPreviousMonth,
            ],
            'current_year' => [
                'label' => 'السنة الحالية (' . Carbon::now()->year . ')',
                'sales_count' => $currentYear['sales_count'],
                'revenue' => $currentYear['revenue'],
                'gross_profit' => $currentYear['profit'],
                'expenses' => $expensesCurrentYear,
                'net_profit' => $currentYear['profit'] - $expensesCurrentYear,
            ],
        ];

        $totalExpenses = Expense::sum('amount');
        $netProfitTotal = $totalProfit - $totalExpenses;

        return view('dashboard', compact(
            'totalProducts', 'totalSales', 'totalProfit', 'totalRevenue',
            'lowStockProducts', 'recentDamages', 'totalDamaged', 'totalLosses', 'recentLosses',
            'periodStats', 'totalExpenses', 'netProfitTotal'
        ));
    }

    /**
     * Export dashboard statistics to Excel (CSV)
     */
    public function exportStats()
    {
        $totalProducts = Product::count();
        $totalSales = SalesStatsService::totalSalesCount();
        $totalProfit = SalesStatsService::totalProfit();
        $totalRevenue = SalesStatsService::totalRevenue();
        $totalPurchases = \App\Models\Purchase::count();
        $totalDamaged = abs(\App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_DAMAGE)->sum('quantity'));
        $totalLosses = \App\Models\Loss::sum('total_loss');
        $totalExpenses = Expense::sum('amount');
        $netProfitTotal = $totalProfit - $totalExpenses;
        $lowStockProducts = Product::all()->filter(fn ($p) => $p->total_stock < 10)->count();

        // إحصائيات الفترات (اليوم، أمس، الشهر الحالي، الشهر السابق، السنة الحالية)
        $today = SalesStatsService::forToday();
        $yesterday = SalesStatsService::forYesterday();
        $currentMonth = SalesStatsService::forCurrentMonth();
        $previousMonth = SalesStatsService::forPreviousMonth();
        $currentYear = SalesStatsService::forCurrentYear();

        $expensesToday = Expense::sumBetween(Carbon::today(), Carbon::today());
        $expensesYesterday = Expense::sumBetween(Carbon::yesterday(), Carbon::yesterday());
        $expensesCurrentMonth = Expense::sumBetween(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $expensesPreviousMonth = Expense::sumBetween(Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth());
        $expensesCurrentYear = Expense::sumBetween(Carbon::now()->startOfYear(), Carbon::now()->endOfYear());

        $periodStats = [
            'today' => [
                'label' => 'اليوم (' . Carbon::today()->format('Y-m-d') . ')',
                'sales_count' => $today['sales_count'],
                'revenue' => $today['revenue'],
                'gross_profit' => $today['profit'],
                'expenses' => $expensesToday,
                'net_profit' => $today['profit'] - $expensesToday,
            ],
            'yesterday' => [
                'label' => 'أمس (' . Carbon::yesterday()->format('Y-m-d') . ')',
                'sales_count' => $yesterday['sales_count'],
                'revenue' => $yesterday['revenue'],
                'gross_profit' => $yesterday['profit'],
                'expenses' => $expensesYesterday,
                'net_profit' => $yesterday['profit'] - $expensesYesterday,
            ],
            'current_month' => [
                'label' => 'الشهر الحالي (' . Carbon::now()->translatedFormat('F Y') . ')',
                'sales_count' => $currentMonth['sales_count'],
                'revenue' => $currentMonth['revenue'],
                'gross_profit' => $currentMonth['profit'],
                'expenses' => $expensesCurrentMonth,
                'net_profit' => $currentMonth['profit'] - $expensesCurrentMonth,
            ],
            'previous_month' => [
                'label' => 'الشهر السابق (' . Carbon::now()->subMonth()->translatedFormat('F Y') . ')',
                'sales_count' => $previousMonth['sales_count'],
                'revenue' => $previousMonth['revenue'],
                'gross_profit' => $previousMonth['profit'],
                'expenses' => $expensesPreviousMonth,
                'net_profit' => $previousMonth['profit'] - $expensesPreviousMonth,
            ],
            'current_year' => [
                'label' => 'السنة الحالية (' . Carbon::now()->year . ')',
                'sales_count' => $currentYear['sales_count'],
                'revenue' => $currentYear['revenue'],
                'gross_profit' => $currentYear['profit'],
                'expenses' => $expensesCurrentYear,
                'net_profit' => $currentYear['profit'] - $expensesCurrentYear,
            ],
        ];

        // Get all products with details
        $products = Product::all();

        // Get all sales
        $sales = Sale::with('product')->latest()->get();

        // Get all purchases
        $purchases = \App\Models\Purchase::with('product')->latest()->get();

        // Get all losses
        $losses = \App\Models\Loss::with('product')->latest()->get();

        // Generate CSV content
        $csvContent = $this->generateCsvContent(
            $totalProducts,
            $totalSales,
            $totalPurchases,
            $totalProfit,
            $totalRevenue,
            $totalDamaged,
            $totalLosses,
            $totalExpenses,
            $netProfitTotal,
            $lowStockProducts,
            $periodStats,
            $products,
            $sales,
            $purchases,
            $losses
        );
        
        $filename = 'إحصائيات_المخزون_' . date('Y-m-d_His') . '.csv';
        
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Generate CSV content for export
     */
    private function generateCsvContent(
        $totalProducts,
        $totalSales,
        $totalPurchases,
        $totalProfit,
        $totalRevenue,
        $totalDamaged,
        $totalLosses,
        $totalExpenses,
        $netProfitTotal,
        $lowStockProducts,
        array $periodStats,
        $products,
        $sales,
        $purchases,
        $losses
    ): string {
        $output = fopen('php://temp', 'r+');

        // Add BOM for UTF-8
        fputs($output, "\xEF\xBB\xBF");

        // Summary Sheet
        fputcsv($output, ['ملخص الإحصائيات'], ',');
        fputcsv($output, ['المؤشر', 'القيمة'], ',');
        fputcsv($output, ['إجمالي المنتجات', $totalProducts], ',');
        fputcsv($output, ['إجمالي المبيعات', $totalSales], ',');
        fputcsv($output, ['إجمالي المشتريات', $totalPurchases], ',');
        fputcsv($output, ['إجمالي الإيرادات', number_format($totalRevenue, 2) . ' ج.م'], ',');
        fputcsv($output, ['إجمالي الأرباح (الربح الإجمالي)', number_format($totalProfit, 2) . ' ج.م'], ',');
        fputcsv($output, ['إجمالي المصاريف', number_format($totalExpenses, 2) . ' ج.م'], ',');
        fputcsv($output, ['الربح الصافي', number_format($netProfitTotal, 2) . ' ج.م'], ',');
        fputcsv($output, ['إجمالي التلف', $totalDamaged . ' وحدة'], ',');
        fputcsv($output, ['إجمالي الخسائر', number_format($totalLosses, 2) . ' ج.م'], ',');
        fputcsv($output, ['منتجات مخزون منخفض', $lowStockProducts], ',');
        fputcsv($output, ['تاريخ التقرير', date('Y-m-d H:i:s')], ',');
        fputcsv($output, [], ','); // Empty row

        // إحصائيات الفترات
        fputcsv($output, ['إحصائيات الفترات'], ',');
        fputcsv($output, ['الفترة', 'عدد المبيعات', 'الإيرادات', 'الربح الإجمالي', 'المصاريف', 'الربح الصافي'], ',');
        foreach ($periodStats as $stat) {
            fputcsv($output, [
                $stat['label'],
                $stat['sales_count'],
                number_format($stat['revenue'], 2) . ' ج.م',
                number_format($stat['gross_profit'], 2) . ' ج.م',
                number_format($stat['expenses'], 2) . ' ج.م',
                number_format($stat['net_profit'], 2) . ' ج.م',
            ], ',');
        }
        fputcsv($output, [], ','); // Empty row
        
        // Products Sheet
        fputcsv($output, ['المنتجات'], ',');
        fputcsv($output, ['ID', 'الاسم', 'SKU', 'الكمية', 'سعر البيع', 'الوصف', 'تاريخ الإنشاء'], ',');
        foreach ($products as $product) {
            fputcsv($output, [
                $product->id,
                $product->name,
                $product->sku,
                $product->total_stock,
                number_format($product->selling_price, 2) . ' ج.م',
                $product->description ?? '-',
                $product->created_at->format('Y-m-d'),
            ], ',');
        }
        fputcsv($output, [], ','); // Empty row
        
        // Sales Sheet
        fputcsv($output, ['المبيعات'], ',');
        fputcsv($output, ['ID', 'المنتج', 'الكمية', 'سعر البيع', 'سعر الشراء', 'الربح', 'المحافظة', 'التاريخ'], ',');
        foreach ($sales as $sale) {
            fputcsv($output, [
                $sale->id,
                $sale->product->name,
                $sale->quantity,
                number_format($sale->selling_price, 2) . ' ج.م',
                number_format($sale->cost_price_at_sale, 2) . ' ج.م',
                number_format($sale->profit, 2) . ' ج.م',
                $sale->governorate ?? '-',
                $sale->created_at->format('Y-m-d H:i'),
            ], ',');
        }
        fputcsv($output, [], ','); // Empty row
        
        // Purchases Sheet
        fputcsv($output, ['المشتريات'], ',');
        fputcsv($output, ['ID', 'المنتج', 'الكمية', 'سعر التكلفة', 'الإجمالي', 'التاريخ'], ',');
        foreach ($purchases as $purchase) {
            fputcsv($output, [
                $purchase->id,
                $purchase->product->name,
                $purchase->quantity,
                number_format($purchase->cost_price, 2) . ' ج.م',
                number_format($purchase->quantity * $purchase->cost_price, 2) . ' ج.م',
                $purchase->created_at->format('Y-m-d H:i'),
            ], ',');
        }
        fputcsv($output, [], ','); // Empty row
        
        // Losses Sheet
        fputcsv($output, ['الخسائر'], ',');
        fputcsv($output, ['ID', 'المنتج', 'الكمية', 'سعر التكلفة', 'إجمالي الخسارة', 'ملاحظة', 'التاريخ'], ',');
        foreach ($losses as $loss) {
            fputcsv($output, [
                $loss->id,
                $loss->product->name,
                $loss->quantity,
                number_format($loss->cost_price_at_loss, 2) . ' ج.م',
                number_format($loss->total_loss, 2) . ' ج.م',
                $loss->note ?? '-',
                $loss->created_at->format('Y-m-d H:i'),
            ], ',');
        }
        
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        return $csvContent;
    }
}
