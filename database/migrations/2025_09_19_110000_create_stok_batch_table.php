<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_batch', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barang_medis', 'id_obat');
            $table->foreignId('id_lokasi')->constrained('lokasi_klinik', 'id');
            $table->string('nomor_batch');
            $table->date('tanggal_kadaluarsa');
            $table->integer('jumlah_unit');
            $table->foreignId('created_by')->constrained('users', 'id');
            $table->string('supplier')->nullable();
            $table->string('nomor_faktur')->nullable();
            $table->date('tanggal_penerimaan');
            $table->timestamps();
            
            // Indeks untuk pencarian cepat
            $table->index(['id_barang', 'id_lokasi', 'tanggal_kadaluarsa']);
        });

        // Tambah kolom status_exp di tabel stok_barang
        Schema::table('stok_barang', function (Blueprint $table) {
            $table->string('status_exp')->nullable()->comment('OK, WARNING, EXPIRED');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_batch');
        
        Schema::table('stok_barang', function (Blueprint $table) {
            $table->dropColumn('status_exp');
        });
    }
};