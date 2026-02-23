<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['sales', 'purchases', 'stock_movements', 'losses'];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('size')->nullable()->after('product_id')->comment('Product size (null for generic transaction)');
                $table->string('color')->nullable()->after('size')->comment('Product color (null for generic transaction)');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['sales', 'purchases', 'stock_movements', 'losses'];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['size', 'color']);
            });
        }
    }
};
