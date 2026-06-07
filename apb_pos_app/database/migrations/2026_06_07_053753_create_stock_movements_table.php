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
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->date('movement_date');

            // IN, OUT, ADJUSTMENT
            $table->string('type', 20);

            // GOODS_RECEIPT, SALES, RETURN, ADJUSTMENT, TRANSFER
            $table->string('source_type', 50)->nullable();

            // id transaksi sumber, contoh goods_receipts.id
            $table->unsignedBigInteger('source_id')->nullable();

            // nomor transaksi sumber, contoh GR-00001
            $table->string('reference_no')->nullable();

            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);

            // saldo setelah transaksi
            $table->integer('balance')->default(0);

            $table->decimal('cost_price', 15, 2)->default(0);
            $table->decimal('sell_price', 15, 2)->default(0);

            $table->text('note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['outlet_id', 'product_id']);
            $table->index(['source_type', 'source_id']);
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
