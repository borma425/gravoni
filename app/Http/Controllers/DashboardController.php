<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardStatsExport;

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

        $filename = 'إحصائيات_المخزون_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download(new DashboardStatsExport(
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
        ), $filename);
    }
}
