<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Services\StockService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
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
        $purchases = Purchase::with(['product', 'returns'])->latest()->paginate(20);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all()->map(function ($product) {
            $product->average_cost = $this->stockService->calculateAverageCost($product);
            return $product;
        });

        return view('purchases.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        $product = Product::findOrFail($request->product_id);

        try {
            // إذا لم يتم إدخال سعر التكلفة، استخدم متوسط التكلفة الحالي
            $costPrice = $request->cost_price;
            if (empty($costPrice) || $costPrice === null) {
                $costPrice = $this->stockService->calculateAverageCost($product);
                // إذا لم يكن هناك متوسط تكلفة (منتج جديد بدون مشتريات سابقة)، استخدم 0
                if ($costPrice <= 0) {
                    return back()->withErrors(['cost_price' => 'يجب إدخال سعر التكلفة للمنتج الجديد'])->withInput();
                }
            }

            $this->stockService->recordPurchase(
                $product,
                $request->quantity,
                $costPrice
            );

            return redirect()->route('purchases.index')
                ->with('success', 'تم تسجيل الشراء بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['product', 'returns']);

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        try {
            $product = $purchase->product;
            
            // Calculate net quantity (original - returned)
            $returnedQuantity = $purchase->returned_quantity;
            $netQuantity = $purchase->quantity - $returnedQuantity;
            
            // Restore product quantity (subtract the net quantity that was added)
            $product->quantity -= $netQuantity;
            $product->save();

            // Delete associated stock movement
            $movement = \App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_PURCHASE)
                ->where('reference_id', $purchase->id)
                ->first();
            
            if ($movement) {
                $movement->delete();
            }

            // Delete associated returns and restore their quantities
            foreach ($purchase->returns as $return) {
                // Restore quantity from return (since return subtracts from stock)
                $product->quantity += abs($return->quantity);
                $return->delete();
            }
            $product->save();

            $purchase->delete();

            return redirect()->route('purchases.index')
                ->with('success', 'تم حذف الشراء بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }
}
