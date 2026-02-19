<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Services\StockService;
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
        $totalSales = Sale::count();
        $totalProfit = Sale::sum('profit');
        $totalRevenue = Sale::get()->sum(function ($sale) {
            return $sale->selling_price * $sale->quantity;
        });
        
        // Get low stock products (less than 10)
        $lowStockProducts = Product::where('quantity', '<', 10)->get();

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

        return view('dashboard', compact('totalProducts', 'totalSales', 'totalProfit', 'totalRevenue', 'lowStockProducts', 'recentDamages', 'totalDamaged', 'totalLosses', 'recentLosses'));
    }

    /**
     * Export dashboard statistics to Excel
     */
    public function exportStats()
    {
        $totalProducts = Product::count();
        $totalSales = Sale::count();
        $totalProfit = Sale::sum('profit');
        $totalRevenue = Sale::get()->sum(function ($sale) {
            return $sale->selling_price * $sale->quantity;
        });
        $totalPurchases = \App\Models\Purchase::count();
        $totalDamaged = abs(\App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_DAMAGE)->sum('quantity'));
        $totalLosses = \App\Models\Loss::sum('total_loss');
        $lowStockProducts = Product::where('quantity', '<', 10)->count();
        
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
            $lowStockProducts,
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
        $lowStockProducts,
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
        fputcsv($output, ['إجمالي الأرباح', number_format($totalProfit, 2) . ' ج.م'], ',');
        fputcsv($output, ['إجمالي التلف', $totalDamaged . ' وحدة'], ',');
        fputcsv($output, ['إجمالي الخسائر', number_format($totalLosses, 2) . ' ج.م'], ',');
        fputcsv($output, ['منتجات مخزون منخفض', $lowStockProducts], ',');
        fputcsv($output, ['تاريخ التقرير', date('Y-m-d H:i:s')], ',');
        fputcsv($output, [], ','); // Empty row
        
        // Products Sheet
        fputcsv($output, ['المنتجات'], ',');
        fputcsv($output, ['ID', 'الاسم', 'SKU', 'الكمية', 'سعر البيع', 'الوصف', 'تاريخ الإنشاء'], ',');
        foreach ($products as $product) {
            fputcsv($output, [
                $product->id,
                $product->name,
                $product->sku,
                $product->quantity,
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
