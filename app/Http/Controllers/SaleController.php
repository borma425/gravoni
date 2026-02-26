<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SalesStatsService;
use App\Services\StockService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /** Statuses considered "sold" (accepted orders) */
    public const SOLD_ORDER_STATUSES = SalesStatsService::SOLD_ORDER_STATUSES;

    /**
     * Display a listing of accepted orders and manual sales.
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->q);
        $search = $q !== '';

        $orderQuery = Order::with('governorate')
            ->whereIn('status', self::SOLD_ORDER_STATUSES);

        if ($search) {
            $orderQuery->where(function ($oq) use ($q) {
                $oq->where('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_address', 'like', "%{$q}%")
                    ->orWhere('tracking_id', 'like', "%{$q}%")
                    ->orWhere('customer_numbers', 'like', "%{$q}%")
                    ->orWhere('items', 'like', "%{$q}%")
                    ->orWhereHas('governorate', fn ($g) => $g->where('name', 'like', "%{$q}%"));
            });
        }

        $orders = (clone $orderQuery)->latest()->paginate(15)->withQueryString();

        $productIds = $orders->flatMap(fn ($o) => collect($o->items ?? [])->pluck('product_id'))->unique()->filter();
        $products = $productIds->isNotEmpty()
            ? Product::whereIn('id', $productIds->map(fn ($id) => (int) $id))->get()->keyBy('id')
            : collect();

        $manualSalesQuery = Sale::with(['product', 'returns']);

        if ($search) {
            $manualSalesQuery->where(function ($sq) use ($q) {
                $sq->where('governorate', 'like', "%{$q}%")
                    ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$q}%"));
            });
        }

        $manualSales = (clone $manualSalesQuery)->latest()->paginate(10)->withQueryString();

        // إحصائيات موحدة: الأوردرات المقبولة + المبيعات اليدوية (عند الحذف يتم خصمها تلقائياً)
        if (!$search) {
            $totalSalesCount = SalesStatsService::totalSalesCount();
            $totalRevenue = SalesStatsService::totalRevenue();
        } else {
            $filteredOrdersQuery = Order::whereIn('status', self::SOLD_ORDER_STATUSES)
                ->where(function ($oq) use ($q) {
                    $oq->where('customer_name', 'like', "%{$q}%")
                        ->orWhere('customer_address', 'like', "%{$q}%")
                        ->orWhere('tracking_id', 'like', "%{$q}%")
                        ->orWhere('customer_numbers', 'like', "%{$q}%")
                        ->orWhere('items', 'like', "%{$q}%")
                        ->orWhereHas('governorate', fn ($g) => $g->where('name', 'like', "%{$q}%"));
                });
            $ordersCount = (clone $filteredOrdersQuery)->count();
            $ordersRevenue = (clone $filteredOrdersQuery)->get()->sum(fn ($o) => $o->items_revenue);
            $manualSalesCount = (clone $manualSalesQuery)->count();
            $manualSalesRevenue = (clone $manualSalesQuery)->get()->sum(fn ($s) => $s->selling_price * $s->quantity);
            $totalSalesCount = $ordersCount + $manualSalesCount;
            $totalRevenue = $ordersRevenue + $manualSalesRevenue;
        }

        return view('sales.index', compact('orders', 'manualSales', 'totalSalesCount', 'totalRevenue', 'products', 'q'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();

        return view('sales.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSaleRequest $request)
    {
        $product = Product::findOrFail($request->product_id);

        try {
            // إذا لم يتم إدخال سعر البيع، استخدم السعر الافتراضي من المنتج
            $sellingPrice = $request->selling_price ?? $product->selling_price;

            $sale = $this->stockService->recordSale(
                $product,
                $request->quantity,
                $sellingPrice,
                $request->governorate,
                $request->size,
                $request->color
            );

            return redirect()->route('sales.index')
                ->with('success', 'تم تسجيل البيع بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['product', 'returns']);

        return view('sales.show', compact('sale'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        try {
            $product = $sale->product;
            
            // Calculate net quantity (original - returned)
            $returnedQuantity = $sale->returned_quantity;
            $netQuantity = $sale->quantity - $returnedQuantity;
            
            // Restore product quantity (add back the net quantity that was sold)
            $this->stockService->updateProductStock($product, $netQuantity, $sale->size, $sale->color);

            // Delete associated stock movement
            $movement = \App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_SALE)
                ->where('reference_id', $sale->id)
                ->first();
            
            if ($movement) {
                $movement->delete();
            }

            // Delete associated returns and restore their quantities
            foreach ($sale->returns as $return) {
                // Subtract quantity from return (since return adds to stock)
                $this->stockService->updateProductStock($product, -$return->quantity, $return->size, $return->color);
                $return->delete();
            }

            $sale->delete();

            return redirect()->route('sales.index')
                ->with('success', 'تم حذف البيع بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete an accepted order from the sales list (removes the order permanently).
     */
    public function destroyOrder(Order $order)
    {
        if (!in_array($order->status, self::SOLD_ORDER_STATUSES, true)) {
            return redirect()->route('sales.index')->with('error', 'لا يمكن حذف طلب غير مقبول من صفحة المبيعات.');
        }
        $order->delete();
        return redirect()->route('sales.index')->with('success', 'تم حذف المبيعة بنجاح.');
    }
}
