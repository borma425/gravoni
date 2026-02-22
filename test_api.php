
$p = App\Models\Product::orderBy('id', 'desc')->first();
if ($p) {
    echo "Product found: " . $p->id . "\n";
    $p->available_sizes = [
        [
            "size" => "M",
            "chest_width_cm" => 56,
            "weight_kg" => ["min" => 65, "max" => 75],
            "height_cm" => ["min" => 165, "max" => 175]
        ],
        [
            "size" => "L",
            "chest_width_cm" => 60,
            "weight_kg" => ["min" => 75, "max" => 85],
            "height_cm" => ["min" => 175, "max" => 185]
        ]
    ];
    $p->save();

    // Now instantiate controller and call show
    $controller = app()->make(App\Http\Controllers\Api\ProductApiController::class);
    $response = $controller->show((string)$p->id);
    echo $response->getContent() . "\n";
} else {
    echo "No product found to test.\n";
}
