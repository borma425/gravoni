<?php
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$dummyVideoPath = sys_get_temp_dir() . '/dummy.mp4';
file_put_contents($dummyVideoPath, 'dummy video data');
$file = new UploadedFile($dummyVideoPath, 'dummy.mp4', 'video/mp4', null, true);

$data = [
    'name' => 'API Test Product Nested',
    'sku' => 'TEST-SKU-NESTED-' . time(),
    'selling_price' => 100,
    'quantity' => 10,
    'available_sizes' => [
        [
            'size' => 'M',
            'colors' => [
                ['color' => 'Red', 'stock' => 5],
                ['color' => 'Blue', 'stock' => 5]
            ]
        ]
    ]
];

$product = Product::create($data);
$product->videos = [$file->store('products/videos', 'public')];
$product->save();

echo "Product Created: " . $product->id . "\n";
echo "SKU: " . $product->sku . "\n";
