<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini menggabungkan semua perubahan pada tabel detail_permintaan_barang:
     * - Create table original (2025_09_03_100907)
     * - Add additional fields (2025_09_19_075903)
     */
    public function up(): void
    {
        Schema::create('detail_permintaan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_permintaan')->constrained('permintaan_barang')->onDelete('cascade');
            $table->foreignId('id_barang')->nullable()->constrained('barang_medis', 'id_obat')->onDelete('restrict');
            
            // Detail permintaan barang existing
            $table->integer('jumlah_diminta');
            $table->string('satuan_diminta', 100)->nullable();
            $table->string('kemasan_diminta', 150)->nullable();
            $table->string('catatan', 255)->nullable();
            $table->integer('jumlah_disetujui')->nullable();
            
            // Detail barang baru (jika tidak ada di master)
            $table->string('nama_barang_baru')->nullable();
            $table->enum('tipe_barang_baru', ['OBAT', 'ALKES'])->nullable();
            $table->string('satuan_barang_baru', 100)->nullable();
            $table->string('kemasan_barang_baru', 150)->nullable();
            $table->string('catatan_barang_baru', 255)->nullable();
            
            $table->timestamps();

            $table->unique(['id_permintaan', 'id_barang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_permintaan_barang');
    }
};