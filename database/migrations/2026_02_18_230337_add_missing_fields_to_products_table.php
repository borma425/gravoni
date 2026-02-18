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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('selling_price');
            $table->integer('stock_count')->nullable()->after('quantity');
            $table->json('available_sizes')->nullable()->after('stock_count');
            $table->json('available_colors')->nullable()->after('available_sizes');
            $table->string('sample')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['price', 'stock_count', 'available_sizes', 'available_colors', 'sample']);
        });
    }
};
