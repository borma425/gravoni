<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pinecone integration - matches Store-agent pattern.
 * Stores minimal data: id + name only. Full product details fetched from API when needed.
 *
 * @see /opt/lampp/htdocs/Store-agent/src/scripts/sync-db.ts
 * @see /opt/lampp/htdocs/Store-agent/src/infrastructure/services/StoreInventoryService.ts
 */
class PineconeService
{
    protected string $apiKey;
    protected string $indexHost;
    protected int $embeddingDimension;
    protected string $namespace;

    public function __construct()
    {
        $this->apiKey = config('services.pinecone.api_key');
        $this->indexHost = config('services.pinecone.index_host');
        $this->embeddingDimension = config('services.pinecone.embedding_dimension', 1024);
        $this->namespace = config('services.pinecone.namespace', '');
    }

    /**
     * Upsert (create or update) a product in Pinecone.
     * Sends only: id (vector id) + name (metadata) - same as Store-agent.
     */
    public function upsertProduct(Product $product): bool
    {
        try {
            $vector = $this->generateEmbedding($product);

            $payload = [
                'vectors' => [
                    [
                        'id' => (string) $product->id,
                        'values' => $vector,
                        'metadata' => [
                            'name' => $product->name,
                        ],
                    ],
                ],
            ];

            if ($this->namespace !== '') {
                $payload['namespace'] = $this->namespace;
            }

            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->indexHost}/vectors/upsert", $payload);

            if ($response->successful()) {
                Log::info("Product {$product->id} synced to Pinecone successfully");
                return true;
            }

            Log::error("Failed to sync product {$product->id} to Pinecone: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Error syncing product {$product->id} to Pinecone: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a product from Pinecone.
     * Deletes both id formats for migration compatibility (legacy "product_{id}" and new "{id}").
     */
    public function deleteProduct(int $productId): bool
    {
        try {
            $ids = [(string) $productId, "product_{$productId}"];
            $payload = [
                'ids' => $ids,
            ];

            if ($this->namespace !== '') {
                $payload['namespace'] = $this->namespace;
            }

            $response = Http::withHeaders([
                'Api-Key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->indexHost}/vectors/delete", $payload);

            if ($response->successful()) {
                Log::info("Product {$productId} deleted from Pinecone successfully");
                return true;
            }

            $responseBody = $response->json();

            if (isset($responseBody['code']) && $responseBody['code'] === 5) {
                Log::debug("Product {$productId} - Namespace not found in Pinecone (product was not synced yet)");
                return true;
            }

            Log::error("Failed to delete product {$productId} from Pinecone: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Error deleting product {$productId} from Pinecone: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Build embedding text - same structure as Store-agent sync-db.ts
     * "Product: {name}. Category: Clothing. Description: {description}. Available Sizes: {sizes}"
     */
    protected function buildEmbeddingText(Product $product): string
    {
        $sizes = is_array($product->available_sizes)
            ? implode(', ', $product->available_sizes)
            : ($product->available_sizes ?? '');

        return sprintf(
            'Product: %s. Category: Clothing. Description: %s. Available Sizes: %s',
            $product->name,
            $product->description ?? '',
            $sizes
        );
    }

    /**
     * Generate embedding vector from product data.
     */
    protected function generateEmbedding(Product $product): array
    {
        $text = $this->buildEmbeddingText($product);

        if (config('services.openai.api_key')) {
            return $this->generateOpenAIEmbedding($text);
        }

        return $this->generateSimpleVector($text);
    }

    protected function generateOpenAIEmbedding(string $text): array
    {
        try {
            $payload = [
                'input' => $text,
                'model' => 'text-embedding-3-small',
            ];

            if ($this->embeddingDimension !== 1536) {
                $payload['dimensions'] = $this->embeddingDimension;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/embeddings', $payload);

            if ($response->successful()) {
                return $response->json()['data'][0]['embedding'];
            }
        } catch (\Exception $e) {
            Log::warning("Failed to generate OpenAI embedding: " . $e->getMessage());
        }

        return $this->generateSimpleVector($text);
    }

    protected function generateSimpleVector(string $text): array
    {
        $hash = md5($text);
        $vector = [];

        for ($i = 0; $i < $this->embeddingDimension; $i++) {
            $seed = hexdec(substr($hash, $i % 32, 2)) + $i;
            $vector[] = (sin($seed) + 1) / 2;
        }

        return $vector;
    }

    /**
     * Sync all products to Pinecone.
     */
    public function syncAllProducts(): array
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
