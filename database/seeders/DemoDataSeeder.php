<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stockService = app(StockService::class);
        // إنشاء 3 منتجات
        $product1 = Product::create([
            'name' => 'لابتوب Dell Inspiron 15',
            'sku' => 'LAP-DELL-001',
            'selling_price' => 15000.00,
            'quantity' => 0,
            'description' => 'لابتوب Dell Inspiron 15 بمعالج Intel Core i5 وذاكرة 8GB وSSD 256GB',
        ]);

        $product2 = Product::create([
            'name' => 'هاتف Samsung Galaxy A54',
            'sku' => 'PHONE-SAM-001',
            'selling_price' => 8500.00,
            'quantity' => 0,
            'description' => 'هاتف Samsung Galaxy A54 بشاشة 6.4 بوصة وذاكرة 128GB وكاميرا 50MP',
        ]);

        $product3 = Product::create([
            'name' => 'سماعات AirPods Pro',
            'sku' => 'AUD-APP-001',
            'selling_price' => 4500.00,
            'quantity' => 0,
            'description' => 'سماعات AirPods Pro مع إلغاء الضوضاء النشط ومقاومة الماء',
        ]);

        // إضافة مشتريات للمنتج الأول (لابتوب)
        $stockService->recordPurchase($product1, 5, 12000.00);
        $stockService->recordPurchase($product1, 3, 11800.00);
        
        // إضافة مبيعات للمنتج الأول
        $stockService->recordSale($product1, 2, 15000.00);
        $stockService->recordSale($product1, 1, 14800.00);

        // إضافة مشتريات للمنتج الثاني (هاتف)
        $stockService->recordPurchase($product2, 10, 7000.00);
        $stockService->recordPurchase($product2, 5, 6800.00);
        
        // إضافة مبيعات للمنتج الثاني
        $stockService->recordSale($product2, 4, 8500.00);
        $stockService->recordSale($product2, 3, 8400.00);
        $stockService->recordSale($product2, 2, 8500.00);

        // إضافة مشتريات للمنتج الثالث (سماعات)
        $stockService->recordPurchase($product3, 15, 3500.00);
        $stockService->recordPurchase($product3, 10, 3400.00);
        
        // إضافة مبيعات للمنتج الثالث
        $stockService->recordSale($product3, 8, 4500.00);
        $stockService->recordSale($product3, 5, 4400.00);
        $stockService->recordSale($product3, 2, 4500.00);

        $this->command->info('تم إضافة بيانات Demo بنجاح!');
        $this->command->info('المنتجات:');
        $this->command->info('- ' . $product1->name . ' (المخزون: ' . $product1->fresh()->quantity . ')');
        $this->command->info('- ' . $product2->name . ' (المخزون: ' . $product2->fresh()->quantity . ')');
        $this->command->info('- ' . $product3->name . ' (المخزون: ' . $product3->fresh()->quantity . ')');
    }
}
