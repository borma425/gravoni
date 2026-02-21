<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('samples')->nullable()->after('description');
        });

        // Migrate existing sample (string) to samples (json array)
        $products = DB::table('products')->whereNotNull('sample')->get();
        foreach ($products as $product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['samples' => json_encode([$product->sample])]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sample');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sample')->nullable()->after('description');
        });

        // Migrate back: use first image as sample
        $products = DB::table('products')->whereNotNull('samples')->get();
        foreach ($products as $product) {
            $samples = json_decode($product->samples, true);
            $first = is_array($samples) && !empty($samples) ? $samples[0] : null;
            if ($first) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['sample' => $first]);
            }
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('samples');
        });
    }
};
