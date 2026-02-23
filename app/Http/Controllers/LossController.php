<?php

namespace App\Http\Controllers;

use App\Models\Loss;
use App\Services\StockService;
use Illuminate\Http\Request;

class LossController extends Controller
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
        $losses = Loss::with('product')->latest()->paginate(20);

        return view('losses.index', compact('losses'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Loss $loss)
    {
        $loss->load('product');

        return view('losses.show', compact('loss'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loss $loss)
    {
        try {
            // Delete associated stock movement if exists
            if ($loss->stock_movement_id) {
                $movement = $loss->stockMovement;
                if ($movement) {
                    $movement->delete();
                }
            }

            // Restore product quantity and specific size/color stock
            $product = $loss->product;
            $this->stockService->updateProductStock($product, $loss->quantity, $loss->size, $loss->color);

            $loss->delete();

            return redirect()->route('losses.index')
                ->with('success', 'تم حذف الخسارة بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
        }
    }
}
