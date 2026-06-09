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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('to_outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->string('transfer_no')->unique();
            $table->date('transfer_date');

            // draft, approved, received
            $table->enum('status', ['draft', 'approved', 'received'])->default('draft');

            $table->text('note')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();

            $table->timestamps();

            $table->index('transfer_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
