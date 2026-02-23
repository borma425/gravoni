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
     * Handle AJAX media upload (images or videos per color).
     * Returns the stored path so the frontend can embed it in hidden inputs.
     */
    public function uploadMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:20480',
            'type' => 'required|in:image,video',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $file = $request->file('file');
        if (!$file || !$file->isValid()) {
            return response()->json(['message' => 'Invalid file.'], 400);
        }

        // Validate mime type based on type param
        $type = $request->input('type');
        if ($type === 'image') {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            $folder = 'products/colors/images';
        } else {
            $allowedMimes = ['video/mp4', 'video/quicktime', 'video/ogg'];
            $folder = 'products/colors/videos';
        }

        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return response()->json(['message' => 'نوع الملف غير مسموح به.'], 422);
        }

        $path = $file->store($folder, 'public');
        if ($path) {
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
                'type' => $type,
            ]);
        }

        return response()->json(['message' => 'Failed to upload file.'], 500);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        \Log::info('Store Request Data', ['all' => $request->all(), 'validated' => $request->validated()]);

        $data = $request->validated();
        $data['quantity'] = 0;

        // Media is already stored via AJAX and paths are embedded in available_sizes JSON
        // No standalone samples/videos handling needed
        $data['samples'] = [];
        $data['videos'] = [];

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
        \Log::info('Update Request Data', ['all' => $request->all(), 'validated' => $request->validated()]);

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

        // Delete old media files that are no longer referenced
        $this->cleanupOrphanedMedia($product, $data['available_sizes'] ?? []);

        // Media paths are already in available_sizes JSON (pre-uploaded via AJAX)
        // Clear standalone columns
        $data['samples'] = [];
        $data['videos'] = [];

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete all color media from storage
        $this->deleteAllProductMedia($product);

        // Also delete any legacy standalone media
        foreach (($product->samples ?? []) as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        foreach (($product->videos ?? []) as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }

    /**
     * Delete all media files from all colors in a product's available_sizes.
     */
    private function deleteAllProductMedia(Product $product): void
    {
        $sizes = $product->available_sizes ?? [];
        foreach ($sizes as $size) {
            foreach (($size['colors'] ?? []) as $color) {
                foreach (($color['images'] ?? []) as $path) {
                    if (is_string($path) && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                foreach (($color['videos'] ?? []) as $path) {
                    if (is_string($path) && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        }
    }

    /**
     * Delete media files that existed in the old product data but are no longer
     * present in the updated available_sizes.
     */
    private function cleanupOrphanedMedia(Product $product, array $newSizes): void
    {
        // Collect all media paths from the NEW data
        $newPaths = [];
        foreach ($newSizes as $size) {
            foreach (($size['colors'] ?? []) as $color) {
                foreach (($color['images'] ?? []) as $p) {
                    if (is_string($p)) $newPaths[$p] = true;
                }
                foreach (($color['videos'] ?? []) as $p) {
                    if (is_string($p)) $newPaths[$p] = true;
                }
            }
        }

        // Walk the OLD data and delete anything not in the new set
        $oldSizes = $product->available_sizes ?? [];
        foreach ($oldSizes as $size) {
            foreach (($size['colors'] ?? []) as $color) {
                foreach (($color['images'] ?? []) as $path) {
                    if (is_string($path) && !isset($newPaths[$path]) && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
                foreach (($color['videos'] ?? []) as $path) {
                    if (is_string($path) && !isset($newPaths[$path]) && Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        }
    }
}
