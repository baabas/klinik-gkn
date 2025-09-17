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
        Schema::table('barang_medis', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_medis', 'packaging_type')) {
                $table->string('packaging_type', 100)->nullable()->after('kemasan');
            }

            if (!Schema::hasColumn('barang_medis', 'packaging_unit')) {
                $table->string('packaging_unit', 50)->nullable()->after('packaging_type');
            }

            if (!Schema::hasColumn('barang_medis', 'packaging_quantity')) {
                $table->unsignedInteger('packaging_quantity')->nullable()->after('packaging_unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_medis', function (Blueprint $table) {
            if (Schema::hasColumn('barang_medis', 'packaging_quantity')) {
                $table->dropColumn('packaging_quantity');
            }

            if (Schema::hasColumn('barang_medis', 'packaging_unit')) {
                $table->dropColumn('packaging_unit');
            }

            if (Schema::hasColumn('barang_medis', 'packaging_type')) {
                $table->dropColumn('packaging_type');
            }
        });
    }
};
