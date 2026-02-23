<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::select([
            'id', 'name', 'sku', 'selling_price', 'discounted_price',
            'quantity', 'description', 'available_sizes', 'available_colors',
            'created_at', 'updated_at'
        ])->get();

        $formattedProducts = $products->map(fn($p) => $this->formatProduct($p));

        return response()->json([
            'success' => true,
            'data' => $formattedProducts,
            'count' => $formattedProducts->count()
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function show(string $id): JsonResponse
    {
        $product = Product::select([
            'id', 'name', 'sku', 'selling_price', 'discounted_price',
            'description', 'available_sizes', 'available_colors',
            'created_at', 'updated_at'
        ])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatProduct($product)
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    private function formatProduct(Product $product): array
    {
        // --- Sizes (with color/stock, no media) ---
        $sizes = $product->available_sizes ?? [];
        if (!empty($sizes) && !array_is_list($sizes)) {
            $sizes = array_values($sizes);
        }
        $availability = array_map(function ($size) {
            if (isset($size['colors']) && is_array($size['colors'])) {
                $size['colors'] = array_values(array_map(function ($c) {
                    return [
                        'color' => $c['color'] ?? '',
                        'stock' => (int) ($c['stock'] ?? 0),
                    ];
                }, $size['colors']));
            } else {
                $size['colors'] = [];
            }
            return $size;
        }, $sizes);

        // --- Colors (with media URLs) ---
        $rawColors = $product->available_colors ?? [];
        if (!empty($rawColors) && !array_is_list($rawColors)) {
            $rawColors = array_values($rawColors);
        }
        $colors = array_map(function ($color) {
            $images = [];
            if (isset($color['images']) && is_array($color['images'])) {
                $images = array_values(array_map(
                    fn($p) => asset('storage/' . $p),
                    array_filter($color['images'], fn($p) => is_string($p) && !empty($p))
                ));
            }
            $videos = [];
            if (isset($color['videos']) && is_array($color['videos'])) {
                $videos = array_values(array_map(
                    fn($p) => asset('storage/' . $p),
                    array_filter($color['videos'], fn($p) => is_string($p) && !empty($p))
                ));
            }
            return [
                'color' => $color['color'] ?? '',
                'images' => $images,
                'videos' => $videos,
            ];
        }, $rawColors);

        return [
            'id' => (string) $product->id,
            'name' => $product->name,
            'price' => (float) $product->selling_price,
            'discounted_price' => $product->discounted_price ? (float) $product->discounted_price : null,
            'availability' => $availability,
            'colors' => $colors,
            'description' => $product->description ?? '',
            'sku' => $product->sku,
            'created_at' => $product->created_at->toISOString(),
            'updated_at' => $product->updated_at->toISOString(),
        ];
    }
}
