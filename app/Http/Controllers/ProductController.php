<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
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
        $products = Product::paginate(20);
        
        // Calculate average cost for each product
        $products->getCollection()->transform(function ($product) {
            $product->average_cost = $this->stockService->calculateAverageCost($product);
            return $product;
        });

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        Product::create($request->validated());

        return redirect()->route('products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->average_cost = $this->stockService->calculateAverageCost($product);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {
        $data = $request->validated();
        
        // Handle SKU uniqueness check for update
        if ($product->sku !== $data['sku']) {
            $validator = Validator::make($data, [
                'sku' => 'unique:products,sku,' . $product->id,
            ]);
            
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }
}
