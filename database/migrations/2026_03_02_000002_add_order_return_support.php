<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedInteger('order_item_index')->nullable()->after('reference_id');
        });
        \DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('purchase','sale','sales_return','purchase_return','damage','adjustment','order_return')");
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn('order_item_index');
        });
        \DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('purchase','sale','sales_return','purchase_return','damage','adjustment')");
    }
};
