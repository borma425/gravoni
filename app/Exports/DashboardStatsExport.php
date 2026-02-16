<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DashboardStatsExport implements WithMultipleSheets
{
    protected $totalProducts;
    protected $totalSales;
    protected $totalPurchases;
    protected $totalProfit;
    protected $totalRevenue;
    protected $totalDamaged;
    protected $totalLosses;
    protected $lowStockProducts;
    protected $products;
    protected $sales;
    protected $purchases;
    protected $losses;

    public function __construct(
        $totalProducts,
        $totalSales,
        $totalPurchases,
        $totalProfit,
        $totalRevenue,
        $totalDamaged,
        $totalLosses,
        $lowStockProducts,
        $products,
        $sales,
        $purchases,
        $losses
    ) {
        $this->totalProducts = $totalProducts;
        $this->totalSales = $totalSales;
        $this->totalPurchases = $totalPurchases;
        $this->totalProfit = $totalProfit;
        $this->totalRevenue = $totalRevenue;
        $this->totalDamaged = $totalDamaged;
        $this->totalLosses = $totalLosses;
        $this->lowStockProducts = $lowStockProducts;
        $this->products = $products;
        $this->sales = $sales;
        $this->purchases = $purchases;
        $this->losses = $losses;
    }

    public function sheets(): array
    {
        return [
            new StatsSummarySheet(
                $this->totalProducts,
                $this->totalSales,
                $this->totalPurchases,
                $this->totalProfit,
                $this->totalRevenue,
                $this->totalDamaged,
                $this->totalLosses,
                $this->lowStockProducts
            ),
            new ProductsSheet($this->products),
            new SalesSheet($this->sales),
            new PurchasesSheet($this->purchases),
            new LossesSheet($this->losses),
        ];
    }
}

class StatsSummarySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $totalProducts;
    protected $totalSales;
    protected $totalPurchases;
    protected $totalProfit;
    protected $totalRevenue;
    protected $totalDamaged;
    protected $totalLosses;
    protected $lowStockProducts;

    public function __construct(
        $totalProducts,
        $totalSales,
        $totalPurchases,
        $totalProfit,
        $totalRevenue,
        $totalDamaged,
        $totalLosses,
        $lowStockProducts
    ) {
        $this->totalProducts = $totalProducts;
        $this->totalSales = $totalSales;
        $this->totalPurchases = $totalPurchases;
        $this->totalProfit = $totalProfit;
        $this->totalRevenue = $totalRevenue;
        $this->totalDamaged = $totalDamaged;
        $this->totalLosses = $totalLosses;
        $this->lowStockProducts = $lowStockProducts;
    }

    public function collection()
    {
        return collect([
            ['المؤشر', 'القيمة'],
            ['إجمالي المنتجات', $this->totalProducts],
            ['إجمالي المبيعات', $this->totalSales],
            ['إجمالي المشتريات', $this->totalPurchases],
            ['إجمالي الإيرادات', number_format($this->totalRevenue, 2) . ' ج.م'],
            ['إجمالي الأرباح', number_format($this->totalProfit, 2) . ' ج.م'],
            ['إجمالي التلف', $this->totalDamaged . ' وحدة'],
            ['إجمالي الخسائر', number_format($this->totalLosses, 2) . ' ج.م'],
            ['منتجات مخزون منخفض', $this->lowStockProducts],
            ['', ''],
            ['تاريخ التقرير', date('Y-m-d H:i:s')],
        ]);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'ملخص الإحصائيات';
    }
}

class ProductsSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'quantity' => $product->quantity,
                'selling_price' => number_format($product->selling_price, 2) . ' ج.م',
                'description' => $product->description ?? '-',
                'created_at' => $product->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'الاسم', 'SKU', 'الكمية', 'سعر البيع', 'الوصف', 'تاريخ الإنشاء'];
    }

    public function title(): string
    {
        return 'المنتجات';
    }
}

class SalesSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales->map(function ($sale) {
            return [
                'id' => $sale->id,
                'product_name' => $sale->product->name,
                'quantity' => $sale->quantity,
                'selling_price' => number_format($sale->selling_price, 2) . ' ج.م',
                'cost_price' => number_format($sale->cost_price_at_sale, 2) . ' ج.م',
                'profit' => number_format($sale->profit, 2) . ' ج.م',
                'governorate' => $sale->governorate ?? '-',
                'created_at' => $sale->created_at->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'المنتج', 'الكمية', 'سعر البيع', 'سعر الشراء', 'الربح', 'المحافظة', 'التاريخ'];
    }

    public function title(): string
    {
        return 'المبيعات';
    }
}

class PurchasesSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $purchases;

    public function __construct($purchases)
    {
        $this->purchases = $purchases;
    }

    public function collection()
    {
        return $this->purchases->map(function ($purchase) {
            return [
                'id' => $purchase->id,
                'product_name' => $purchase->product->name,
                'quantity' => $purchase->quantity,
                'cost_price' => number_format($purchase->cost_price, 2) . ' ج.م',
                'total' => number_format($purchase->quantity * $purchase->cost_price, 2) . ' ج.م',
                'created_at' => $purchase->created_at->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'المنتج', 'الكمية', 'سعر التكلفة', 'الإجمالي', 'التاريخ'];
    }

    public function title(): string
    {
        return 'المشتريات';
    }
}

class LossesSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $losses;

    public function __construct($losses)
    {
        $this->losses = $losses;
    }

    public function collection()
    {
        return $this->losses->map(function ($loss) {
            return [
                'id' => $loss->id,
                'product_name' => $loss->product->name,
                'quantity' => $loss->quantity,
                'cost_price_at_loss' => number_format($loss->cost_price_at_loss, 2) . ' ج.م',
                'total_loss' => number_format($loss->total_loss, 2) . ' ج.م',
                'note' => $loss->note ?? '-',
                'created_at' => $loss->created_at->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return ['ID', 'المنتج', 'الكمية', 'سعر التكلفة', 'إجمالي الخسارة', 'ملاحظة', 'التاريخ'];
    }

    public function title(): string
    {
        return 'الخسائر';
    }
}

