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
            $table->string('satuan_diminta', 100)->nullable()->after('jumlah_diminta');
            $table->string('kemasan_diminta', 150)->nullable()->after('satuan_diminta');
            $table->string('catatan', 255)->nullable()->after('kemasan_diminta');
            $table->string('kemasan_barang_baru', 150)->nullable()->after('satuan_barang_baru');
            $table->string('catatan_barang_baru', 255)->nullable()->after('kemasan_barang_baru');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_permintaan_barang', function (Blueprint $table) {
            $table->dropColumn([
                'satuan_diminta',
                'kemasan_diminta',
                'catatan',
                'kemasan_barang_baru',
                'catatan_barang_baru',
            ]);
        });
    }
};
