<?php
use App\Models\Product;

$product = Product::orderBy('id', 'desc')->first();
echo "ID: " . $product->id . "\n";
echo "SKU: " . $product->sku . "\n";
echo "Availability: " . json_encode($product->available_sizes, JSON_UNESCAPED_UNICODE) . "\n";
echo "Videos: " . json_encode($product->videos, JSON_UNESCAPED_UNICODE) . "\n";
