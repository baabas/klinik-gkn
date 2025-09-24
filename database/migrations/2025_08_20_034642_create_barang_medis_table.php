<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini menggabungkan semua perubahan pada tabel barang_medis:
     * - Create table original (2025_08_20_034642)
     * - Add new fields (2025_09_23_091430)
     * - Remove tanggal (2025_09_23_091828)
     * - Remove jumlah_kemasan (2025_09_23_092600)
     * - Update tipe enum (2025_09_23_094135)
     * - Remove duplicate kategori columns (2025_09_23_094354)
     */
    public function up(): void
    {
        Schema::create('barang_medis', function (Blueprint $table) {
            $table->id('id_obat');
            $table->string('kode_obat', 50)->unique();
            $table->string('nama_obat');
            $table->string('satuan', 50);
            $table->string('kemasan', 100)->nullable();
            
            // Kolom kategori_barang (menggantikan 'tipe' dan 'kategori')
            $table->string('kategori_barang', 100)->nullable();
            
            // Field detail kemasan
            $table->integer('isi_kemasan_jumlah')->nullable();
            $table->string('isi_kemasan_satuan', 50)->nullable();
            $table->integer('isi_per_satuan')->nullable();
            $table->string('satuan_terkecil', 50)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang_medis');
    }
};