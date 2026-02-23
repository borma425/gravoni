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

    public function index()
    {
        $products = Product::paginate(20);
        $products->getCollection()->transform(function ($product) {
            $product->average_cost = $this->stockService->calculateAverageCost($product);
            return $product;
        });
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    /**
     * Handle AJAX media upload (images or videos per color).
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
        $data = $request->validated();
        $data['quantity'] = 0;

        // Normalize arrays to sequential
        $data['available_sizes'] = $this->normalizeSizes($data['available_sizes'] ?? null);
        $data['available_colors'] = $this->normalizeColors($data['available_colors'] ?? null);

        // Clear legacy standalone columns
        $data['samples'] = [];
        $data['videos'] = [];

        $product = Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function show(Product $product)
    {
        $product->average_cost = $this->stockService->calculateAverageCost($product);
        return view('products.show', compact('product'));
    }

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

        // Normalize arrays
        $data['available_sizes'] = $this->normalizeSizes($data['available_sizes'] ?? null);
        $data['available_colors'] = $this->normalizeColors($data['available_colors'] ?? null);

        // Handle SKU uniqueness check for update
        if ($product->sku !== $data['sku']) {
            $validator = Validator::make($data, [
                'sku' => 'unique:products,sku,' . $product->id,
            ]);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        // Delete orphaned media files (media that was in old available_colors but not in new)
        $this->cleanupOrphanedMedia($product, $data['available_colors'] ?? []);

        // Clear legacy standalone columns
        $data['samples'] = [];
        $data['videos'] = [];

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        $this->deleteAllProductMedia($product);

        // Delete any legacy standalone media
        foreach (($product->samples ?? []) as $path) {
            if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
        }
        foreach (($product->videos ?? []) as $path) {
            if (Storage::disk('public')->exists($path)) Storage::disk('public')->delete($path);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'تم حذف المنتج بنجاح');
    }

    // ================================================================
    // Helpers
    // ================================================================

    private function normalizeSizes(?array $sizes): ?array
    {
        if (!$sizes) return null;
        $sizes = array_values($sizes);
        foreach ($sizes as &$size) {
            if (isset($size['colors']) && is_array($size['colors'])) {
                $size['colors'] = array_values($size['colors']);
            }
        }
        unset($size);
        return $sizes;
    }

    private function normalizeColors(?array $colors): ?array
    {
        if (!$colors) return null;
        return array_values($colors);
    }

    /**
     * Delete all media files from available_colors.
     */
    private function deleteAllProductMedia(Product $product): void
    {
        $colors = $product->available_colors ?? [];
        foreach ($colors as $color) {
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

    /**
     * Delete media files from old available_colors that are not in the new data.
     */
    private function cleanupOrphanedMedia(Product $product, array $newColors): void
    {
        // Collect all media paths from NEW data
        $newPaths = [];
        foreach ($newColors as $color) {
            foreach (($color['images'] ?? []) as $p) {
                if (is_string($p)) $newPaths[$p] = true;
            }
            foreach (($color['videos'] ?? []) as $p) {
                if (is_string($p)) $newPaths[$p] = true;
            }
        }

        // Walk OLD data and delete anything missing from new set
        $oldColors = $product->available_colors ?? [];
        foreach ($oldColors as $color) {
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
