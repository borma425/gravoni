<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(): JsonResponse
    {
        $products = Product::select([
            'id',
            'name',
            'sku',
            'selling_price',
            'discounted_price',
            'quantity',
            'description',
            'available_sizes',
            'created_at',
            'updated_at'
        ])->get();

        $formattedProducts = $products->map(function ($product) {
            return $this->formatProduct($product);
        });

        return response()->json([
            'success' => true,
            'data' => $formattedProducts,
            'count' => $formattedProducts->count()
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::select([
            'id',
            'name',
            'sku',
            'selling_price',
            'discounted_price',
            'description',
            'available_sizes',
            'created_at',
            'updated_at'
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

    /**
     * Format a product for API response.
     * Media (images/videos) is nested inside each color within each size.
     */
    private function formatProduct(Product $product): array
    {
        $sizes = $product->available_sizes ?? [];

        // Normalize: ensure it's a sequential array
        if (!empty($sizes) && !array_is_list($sizes)) {
            $sizes = array_values($sizes);
        }

        $availability = array_map(function ($size) {
            if (isset($size['colors']) && is_array($size['colors'])) {
                $size['colors'] = array_values(array_map(function ($color) {
                    // Cast stock to integer
                    $color['stock'] = (int) ($color['stock'] ?? 0);

                    // Convert image paths to full URLs
                    if (isset($color['images']) && is_array($color['images'])) {
                        $color['images'] = array_values(array_map(
                            fn($p) => asset('storage/' . $p),
                            array_filter($color['images'], fn($p) => is_string($p) && !empty($p))
                        ));
                    } else {
                        $color['images'] = [];
                    }

                    // Convert video paths to full URLs
                    if (isset($color['videos']) && is_array($color['videos'])) {
                        $color['videos'] = array_values(array_map(
                            fn($p) => asset('storage/' . $p),
                            array_filter($color['videos'], fn($p) => is_string($p) && !empty($p))
                        ));
                    } else {
                        $color['videos'] = [];
                    }

                    return $color;
                }, $size['colors']));
            } else {
                $size['colors'] = [];
            }
            return $size;
        }, $sizes);

        return [
            'id' => (string) $product->id,
            'name' => $product->name,
            'price' => (float) $product->selling_price,
            'discounted_price' => $product->discounted_price ? (float) $product->discounted_price : null,
            'availability' => $availability,
            'description' => $product->description ?? '',
            'sku' => $product->sku,
            'created_at' => $product->created_at->toISOString(),
            'updated_at' => $product->updated_at->toISOString(),
        ];
    }
}

