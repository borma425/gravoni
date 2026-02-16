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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['purchase', 'sale', 'sales_return', 'purchase_return', 'damage', 'adjustment']);
            $table->integer('quantity'); // يمكن أن يكون موجب أو سالب
            $table->unsignedBigInteger('reference_id')->nullable(); // مرجع للعملية الأصلية (purchase_id أو sale_id)
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
