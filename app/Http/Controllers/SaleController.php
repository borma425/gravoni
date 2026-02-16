<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = Sale::with(['product', 'returns'])->latest()->paginate(20);

        return view('sales.index', compact('sales'));
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
                $request->governorate
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
            $product->quantity += $netQuantity;
            $product->save();

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
                $product->quantity -= $return->quantity;
                $return->delete();
            }
            $product->save();

            $sale->delete();

            return redirect()->route('sales.index')
                ->with('success', 'تم حذف البيع بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }
}
