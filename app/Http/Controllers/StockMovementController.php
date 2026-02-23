<?php

namespace App\Http\Controllers;

use App\Http\Requests\DamageRequest;
use App\Http\Requests\PurchaseReturnRequest;
use App\Http\Requests\SalesReturnRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Show form for sales return
     */
    public function showSalesReturnForm()
    {
        $sales = Sale::with('product')->latest()->get();

        return view('stock-movements.sales-return', compact('sales'));
    }

    /**
     * Record sales return
     */
    public function recordSalesReturn(SalesReturnRequest $request)
    {
        $sale = Sale::findOrFail($request->sale_id);

        try {
            $this->stockService->recordSalesReturn($sale, $request->quantity);

            return redirect()->route('stock-movements.sales-return')
                ->with('success', 'تم تسجيل مرتجع البيع بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show form for purchase return
     */
    public function showPurchaseReturnForm()
    {
        $purchases = Purchase::with('product')->latest()->get();

        return view('stock-movements.purchase-return', compact('purchases'));
    }

    /**
     * Record purchase return
     */
    public function recordPurchaseReturn(PurchaseReturnRequest $request)
    {
        $purchase = Purchase::findOrFail($request->purchase_id);

        try {
            $this->stockService->recordPurchaseReturn($purchase, $request->quantity);

            return redirect()->route('stock-movements.purchase-return')
                ->with('success', 'تم تسجيل مرتجع الشراء بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Show form for damage
     */
    public function showDamageForm()
    {
        $products = Product::all();

        return view('stock-movements.damage', compact('products'));
    }

    /**
     * Record damage
     */
    public function recordDamage(DamageRequest $request)
    {
        $product = Product::findOrFail($request->product_id);

        try {
            $result = $this->stockService->recordDamage($product, $request->quantity, $request->note, $request->size, $request->color);

            return redirect()->route('losses.index')
                ->with('success', 'تم تسجيل التلف والخسارة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display a listing of damages
     */
    public function indexDamages()
    {
        $damages = \App\Models\StockMovement::where('type', \App\Models\StockMovement::TYPE_DAMAGE)
            ->with('product')
            ->latest()
            ->paginate(20);

        return view('stock-movements.damages-index', compact('damages'));
    }

    public function destroyDamage(\App\Models\StockMovement $movement)
    {
        try {
            // Find associated loss
            $loss = \App\Models\Loss::where('stock_movement_id', $movement->id)->first();
            
            if ($loss) {
                // Restore product quantity
                $product = $loss->product;
                $this->stockService->updateProductStock($product, abs($movement->quantity), $loss->size, $loss->color);
                
                // Delete loss
                $loss->delete();
            } else {
                // If no loss record, just restore quantity
                $product = $movement->product;
                $this->stockService->updateProductStock($product, abs($movement->quantity), $movement->size, $movement->color);
            }

            $movement->delete();

            return redirect()->route('stock-movements.damage.index')
                ->with('success', 'تم حذف التلف بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }

}
