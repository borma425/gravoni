<?php

namespace App\Console\Commands;

use App\Models\Loss;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Console\Command;

class SyncLossesForDamages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'losses:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إنشاء خسائر للتلفيات القديمة التي لا تحتوي على خسائر';

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        parent::__construct();
        $this->stockService = $stockService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('بدء إنشاء الخسائر للتلفيات القديمة...');

        // Get all damage movements that don't have a loss record
        $damages = StockMovement::where('type', StockMovement::TYPE_DAMAGE)
            ->whereDoesntHave('loss')
            ->with('product')
            ->get();

        if ($damages->isEmpty()) {
            $this->info('لا توجد تلفيات بدون خسائر.');
            return 0;
        }

        $created = 0;
        $errors = 0;

        foreach ($damages as $damage) {
            try {
                $product = $damage->product;
                
                if (!$product) {
                    $this->warn("تلف ID {$damage->id}: المنتج غير موجود");
                    $errors++;
                    continue;
                }

                // Calculate average cost at the time of damage (use current average cost as approximation)
                $averageCost = $this->stockService->calculateAverageCost($product);
                
                // If average cost is 0, try to get from purchases
                if ($averageCost <= 0) {
                    $purchase = \App\Models\Purchase::where('product_id', $product->id)
                        ->where('created_at', '<=', $damage->created_at)
                        ->latest()
                        ->first();
                    
                    if ($purchase) {
                        $averageCost = $purchase->cost_price;
                    } else {
                        $this->warn("تلف ID {$damage->id}: لا يمكن حساب متوسط التكلفة");
                        $errors++;
                        continue;
                    }
                }

                $quantity = abs($damage->quantity);
                $totalLoss = $averageCost * $quantity;

                // Create loss record
                Loss::create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'cost_price_at_loss' => $averageCost,
                    'total_loss' => $totalLoss,
                    'note' => $damage->note,
                    'stock_movement_id' => $damage->id,
                ]);

                $created++;
                $this->info("تم إنشاء خسارة للتلف ID {$damage->id} - المنتج: {$product->name} - الخسارة: {$totalLoss} ج.م");
            } catch (\Exception $e) {
                $this->error("خطأ في التلف ID {$damage->id}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("تم إنشاء {$created} خسارة بنجاح.");
        if ($errors > 0) {
            $this->warn("حدث {$errors} خطأ.");
        }

        return 0;
    }
}
