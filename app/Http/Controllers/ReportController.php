<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Services\StockService;
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
     * Show profit report
     */
    public function profit(Request $request)
    {
        $query = Sale::with('product');

        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $sales = $query->latest()->paginate(50);
        $totalProfit = Sale::when($request->has('from_date') && $request->from_date, function($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from_date);
        })->when($request->has('to_date') && $request->to_date, function($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to_date);
        })->sum('profit');
        $totalSales = Sale::when($request->has('from_date') && $request->from_date, function($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from_date);
        })->when($request->has('to_date') && $request->to_date, function($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to_date);
        })->sum('quantity');
        $totalRevenue = Sale::when($request->has('from_date') && $request->from_date, function($q) use ($request) {
            $q->whereDate('created_at', '>=', $request->from_date);
        })->when($request->has('to_date') && $request->to_date, function($q) use ($request) {
            $q->whereDate('created_at', '<=', $request->to_date);
        })->get()->sum(function ($sale) {
            return $sale->selling_price * $sale->quantity;
        });

        return view('reports.profit', compact('sales', 'totalProfit', 'totalSales', 'totalRevenue'));
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
