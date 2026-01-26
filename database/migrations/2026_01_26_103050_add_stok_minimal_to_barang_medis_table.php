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
            $table->integer('stok_minimal')->default(0)->after('satuan_terkecil');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_medis', function (Blueprint $table) {
            $table->dropColumn('stok_minimal');
        });
    }
};
