<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PineconeService
{
    protected $apiKey;
    protected $indexName;
    protected $indexHost;

    public function __construct()
    {
        $this->apiKey = config('services.pinecone.api_key');
        $this->indexName = config('services.pinecone.index_name');
        $this->indexHost = config('services.pinecone.index_host');
    }

    /**
     * Upsert (create or update) a product in Pinecone
     */
    public function upsertProduct(Product $product)
    {
        try {
            // Generate embedding vector from product data
            $vector = $this->generateEmbedding($product);
            
            // Prepare metadata
            $metadata = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'selling_price' => (float) $product->selling_price,
                'discounted_price' => $product->discounted_price ? (float) $product->discounted_price : null,
                'quantity' => $product->quantity,
                'description' => $product->description ?? '',
                'available_sizes' => $product->available_sizes ?? [],
                'available_colors' => $product->available_colors ?? [],
                'sample' => $product->sample ?? '',
            ];

            // Upsert to Pinecone
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->indexHost}/vectors/upsert", [
                'vectors' => [
                    [
                        'id' => "product_{$product->id}",
                        'values' => $vector,
                        'metadata' => $metadata,
                    ]
                ],
                'namespace' => 'products',
            ]);

            if ($response->successful()) {
                Log::info("Product {$product->id} synced to Pinecone successfully");
                return true;
            } else {
                Log::error("Failed to sync product {$product->id} to Pinecone: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error syncing product {$product->id} to Pinecone: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a product from Pinecone
     */
    public function deleteProduct($productId)
    {
        try {
            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->indexHost}/vectors/delete", [
                'ids' => ["product_{$productId}"],
                'namespace' => 'products',
            ]);

            if ($response->successful()) {
                Log::info("Product {$productId} deleted from Pinecone successfully");
                return true;
            } else {
                Log::error("Failed to delete product {$productId} from Pinecone: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Error deleting product {$productId} from Pinecone: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate embedding vector from product data
     * This creates a simple vector based on product attributes
     * For production, consider using OpenAI embeddings or similar
     */
    protected function generateEmbedding(Product $product)
    {
        // Create a text representation of the product
        $text = implode(' ', array_filter([
            $product->name,
            $product->sku,
            $product->description,
            implode(' ', $product->available_sizes ?? []),
            implode(' ', $product->available_colors ?? []),
        ]));

        // For now, we'll use OpenAI-compatible embedding
        // If you have OpenAI API key, this will generate proper embeddings
        if (config('services.openai.api_key')) {
            return $this->generateOpenAIEmbedding($text);
        }

        // Fallback: Generate a simple hash-based vector (1536 dimensions for OpenAI compatibility)
        return $this->generateSimpleVector($text);
    }

    /**
     * Generate embedding using OpenAI API
     */
    protected function generateOpenAIEmbedding($text)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/embeddings', [
                'input' => $text,
                'model' => 'text-embedding-3-small',
            ]);

            if ($response->successful()) {
                return $response->json()['data'][0]['embedding'];
            }
        } catch (\Exception $e) {
            Log::warning("Failed to generate OpenAI embedding: " . $e->getMessage());
        }

        // Fallback to simple vector
        return $this->generateSimpleVector($text);
    }

    /**
     * Generate a simple vector based on text hash
     * This is a fallback method - for production use proper embeddings
     */
    protected function generateSimpleVector($text, $dimensions = 1536)
    {
        $hash = md5($text);
        $vector = [];
        
        for ($i = 0; $i < $dimensions; $i++) {
            $seed = hexdec(substr($hash, $i % 32, 2)) + $i;
            $vector[] = (sin($seed) + 1) / 2; // Normalize to 0-1 range
        }
        
        return $vector;
    }

    /**
     * Sync all products to Pinecone
     */
    public function syncAllProducts()
    {
        $products = Product::all();
        $synced = 0;
        $failed = 0;

        foreach ($products as $product) {
            if ($this->upsertProduct($product)) {
                $synced++;
            } else {
                $failed++;
            }
        }

        return [
            'synced' => $synced,
            'failed' => $failed,
            'total' => $products->count(),
        ];
    }
}
