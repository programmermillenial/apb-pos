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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('opname_no')->unique();
            $table->date('opname_date');
            
            // draft, in_progress, review, approved, cancelled
            $table->enum('status', ['draft', 'in_progress', 'review', 'approved', 'cancelled'])->default('draft');
            
            // partial (pilih produk tertentu), full (semua produk di outlet)
            $table->enum('type', ['partial', 'full'])->default('partial');
            
            $table->string('pic_name')->nullable(); // Person in charge yang hitung
            $table->text('note')->nullable();
            
            // Link ke stock_adjustment setelah approved
            $table->foreignId('stock_adjustment_id')->nullable()->constrained('stock_adjustments')->nullOnDelete();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            
            $table->index('opname_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
