<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class SyncProductQuantityCommand extends Command
{
    protected $signature = 'products:sync-quantity';

    protected $description = 'مزامنة حقل quantity للمنتجات من available_sizes (البنية الجديدة)';

    public function handle(): int
    {
        $products = Product::all();
        $synced = 0;

        foreach ($products as $product) {
            $sizes = $product->available_sizes ?? [];
            if (empty($sizes)) {
                continue;
            }

            $total = 0;
            foreach ($sizes as $size) {
                $colors = $size['colors'] ?? [];
                foreach ($colors as $color) {
                    $total += (int)($color['stock'] ?? 0);
                }
            }

            if ($product->quantity !== $total) {
                $product->update(['quantity' => $total]);
                $synced++;
            }
        }

        $this->info("تم مزامنة {$synced} منتج.");
        return Command::SUCCESS;
    }
}
