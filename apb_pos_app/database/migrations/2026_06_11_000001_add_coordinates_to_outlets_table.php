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
        Schema::table('outlets', function (Blueprint $table) {
            if (!Schema::hasColumn('outlets', 'latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('address');
            }

            if (!Schema::hasColumn('outlets', 'longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            if (Schema::hasColumn('outlets', 'longitude')) {
                $table->dropColumn('longitude');
            }

            if (Schema::hasColumn('outlets', 'latitude')) {
                $table->dropColumn('latitude');
            }
        });
    }
};
