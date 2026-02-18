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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->text('customer_address');
            $table->json('customer_numbers');
            $table->decimal('delivery_fees', 10, 2)->default(0);
            $table->json('items');
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'delivery_fees_paid', 'shipped'])->default('pending');
            $table->enum('payment_method', ['InstaPay', 'wallet'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
