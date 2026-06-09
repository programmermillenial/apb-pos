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
        // Migrate data dari products ke product_outlets
        DB::statement('
            INSERT INTO product_outlets (product_id, outlet_id, stock, created_at, updated_at)
            SELECT id, outlet_id, stock, NOW(), NOW()
            FROM products
            WHERE outlet_id IS NOT NULL
        ');

        // Drop foreign key constraint terlebih dahulu
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
        });

        // Hapus columns dari products
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['outlet_id', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->constrained('outlets')->nullOnDelete();
            $table->integer('stock')->default(0);
        });

        // Rollback data
        DB::statement('
            UPDATE products p
            SET outlet_id = (SELECT outlet_id FROM product_outlets WHERE product_id = p.id LIMIT 1),
                stock = (SELECT stock FROM product_outlets WHERE product_id = p.id LIMIT 1)
            WHERE id IN (SELECT product_id FROM product_outlets)
        ');

        Schema::dropIfExists('product_outlets');
    }
};
