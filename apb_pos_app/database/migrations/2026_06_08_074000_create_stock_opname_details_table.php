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
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            
            $table->integer('qty_system'); // Stock di sistem saat opname dibuat
            $table->integer('qty_counted')->default(0); // Hasil hitungan fisik
            $table->integer('difference')->default(0); // Selisih (counted - system)
            
            $table->text('note')->nullable();
            
            // Optional: untuk advanced (multi-team counting)
            $table->string('counted_by')->nullable(); // Nama orang yang hitung item ini
            $table->timestamp('counted_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['stock_opname_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_details');
    }
};
