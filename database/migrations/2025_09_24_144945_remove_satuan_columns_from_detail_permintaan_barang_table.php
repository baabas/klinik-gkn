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
        Schema::table('detail_permintaan_barang', function (Blueprint $table) {
            // Remove satuan columns - only use kemasan format
            $table->dropColumn(['satuan_diminta', 'satuan_barang_baru']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_permintaan_barang', function (Blueprint $table) {
            // Restore satuan columns if needed
            $table->string('satuan_diminta', 100)->nullable()->after('jumlah_diminta');
            $table->string('satuan_barang_baru', 100)->nullable()->after('tipe_barang_baru');
        });
    }
};
