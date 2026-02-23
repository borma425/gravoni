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
            'samples',
            'videos',
            'created_at',
            'updated_at'
        ])->get();

        // Format products data
        $formattedProducts = $products->map(function ($product) {
            $samples = $product->samples ?? [];
            $sampleUrls = array_map(fn ($p) => asset('storage/' . $p), $samples);
            
            $videos = $product->videos ?? [];
            $videoUrls = array_map(fn ($v) => asset('storage/' . $v), $videos);

            $availability = array_values($product->available_sizes ?? []);
            $availability = array_map(function($size) {
                if (isset($size['colors']) && is_array($size['colors'])) {
                    $size['colors'] = array_values($size['colors']);
                }
                return $size;
            }, $availability);

            return [
                'id' => (string) $product->id,
                'name' => $product->name,
                'price' => (float) $product->selling_price,
                'discounted_price' => $product->discounted_price ? (float) $product->discounted_price : null,
                'availability' => $availability,
                'stockCount' => (int) $product->quantity,
                'description' => $product->description ?? '',
                'samples' => $sampleUrls,
                'videos' => $videoUrls,
                'sku' => $product->sku,
                'created_at' => $product->created_at->toISOString(),
                'updated_at' => $product->updated_at->toISOString(),
            ];
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
            'quantity',
            'description',
            'available_sizes',
            'samples',
            'videos',
            'created_at',
            'updated_at'
        ])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $samples = $product->samples ?? [];
        $sampleUrls = array_map(fn ($p) => asset('storage/' . $p), $samples);

        $videos = $product->videos ?? [];
        $videoUrls = array_map(fn ($v) => asset('storage/' . $v), $videos);

        $availability = array_values($product->available_sizes ?? []);
        $availability = array_map(function($size) {
            if (isset($size['colors']) && is_array($size['colors'])) {
                $size['colors'] = array_values($size['colors']);
            }
            return $size;
        }, $availability);

        $formattedProduct = [
            'id' => (string) $product->id,
            'name' => $product->name,
            'price' => (float) $product->selling_price,
            'discounted_price' => $product->discounted_price ? (float) $product->discounted_price : null,
            'availability' => $availability,
            'stockCount' => (int) $product->quantity,
            'description' => $product->description ?? '',
            'samples' => $sampleUrls,
            'videos' => $videoUrls,
            'sku' => $product->sku,
            'created_at' => $product->created_at->toISOString(),
            'updated_at' => $product->updated_at->toISOString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $formattedProduct
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
