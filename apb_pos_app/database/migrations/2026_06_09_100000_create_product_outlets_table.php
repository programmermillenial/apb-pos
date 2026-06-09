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
        Schema::create('product_outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();

            $table->integer('stock')->default(0);
            $table->integer('reorder_point')->default(0);

            $table->timestamps();

            // Unique constraint: 1 product hanya bisa 1x per outlet
            $table->unique(['product_id', 'outlet_id']);
            $table->index('outlet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_outlets');
    }
};
