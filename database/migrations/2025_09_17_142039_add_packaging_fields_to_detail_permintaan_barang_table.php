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
            $table->enum('tipe_jumlah_disetujui', ['SATUAN', 'KEMASAN'])->default('SATUAN')->after('jumlah_disetujui');
            $table->unsignedInteger('jumlah_kemasan_disetujui')->nullable()->after('tipe_jumlah_disetujui');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_permintaan_barang', function (Blueprint $table) {
            $table->dropColumn(['tipe_jumlah_disetujui', 'jumlah_kemasan_disetujui']);
        });
    }
};
