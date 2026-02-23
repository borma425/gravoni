<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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
        \Log::info('Upload Request Data', ['all' => $request->all(), 'files' => $request->allFiles()]);
        $data = $request->validated();
        unset($data['samples'], $data['samples_remove'], $data['available_colors_input'], $data['videos'], $data['videos_remove']);

        // Handle multiple sample images
        $data['samples'] = [];
        if ($request->hasFile('samples')) {
            foreach ($request->file('samples') as $file) {
                $data['samples'][] = $file->store('products/samples', 'public');
            }
        }

        // Handle multiple videos
        $data['videos'] = [];
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('products/videos', 'public');
                    if ($path) {
                        $data['videos'][] = $path;
                    }
                }
            }
        }

        // Ensure we don't save an empty array if there were no videos uploaded or all failed
        if (empty($data['videos'])) {
            $data['videos'] = null;
        }

        $product = Product::create($data);

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
        \Log::info('Update Request Data', ['all' => $request->all(), 'files' => $request->allFiles()]);
        $data = $request->validated();
        unset($data['samples'], $data['samples_remove'], $data['available_colors_input'], $data['videos'], $data['videos_remove']);

        // Handle SKU uniqueness check for update
        if ($product->sku !== $data['sku']) {
            $validator = Validator::make($data, [
                'sku' => 'unique:products,sku,' . $product->id,
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        // Handle samples: keep existing minus removed, add new
        $samples = $product->samples ?? [];
        $removeIndices = array_map('intval', (array) $request->input('samples_remove', []));
        foreach ($removeIndices as $idx) {
            if (isset($samples[$idx]) && Storage::disk('public')->exists($samples[$idx])) {
                Storage::disk('public')->delete($samples[$idx]);
                unset($samples[$idx]);
            }
        }
        $samples = array_values($samples);

        if ($request->hasFile('samples')) {
            foreach ($request->file('samples') as $file) {
                $samples[] = $file->store('products/samples', 'public');
            }
        }
        $data['samples'] = $samples;

        // Handle videos: keep existing minus removed, add new
        $videos = $product->videos ?? [];
        // Ensure $videos is an array of strings, filtering out any previous 'false' DB corruptions
        if (is_array($videos)) {
            $videos = array_filter($videos, function($v) { return is_string($v) && !empty($v); });
        } else {
            $videos = [];
        }

        $removeVideoIndices = array_map('intval', (array) $request->input('videos_remove', []));
        foreach ($removeVideoIndices as $idx) {
            if (isset($videos[$idx]) && Storage::disk('public')->exists($videos[$idx])) {
                Storage::disk('public')->delete($videos[$idx]);
                unset($videos[$idx]);
            }
        }
        $videos = array_values($videos);

        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('products/videos', 'public');
                    if ($path) {
                        $videos[] = $path;
                    }
                }
            }
        }
        $data['videos'] = empty($videos) ? null : $videos;

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $productId = $product->id;
        $samples = $product->samples ?? [];

        $product->delete();

        // Delete sample images from storage
        foreach ($samples as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $videos = $product->videos ?? [];
        foreach ($videos as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }
}
