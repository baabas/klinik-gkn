<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penerimaan_barang', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_penerimaan');
            $table->string('nomor_faktur');
            $table->string('supplier');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('id_lokasi')->constrained('lokasi_klinik');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('penerimaan_barang_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_penerimaan')->constrained('penerimaan_barang')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('barang_medis', 'id_obat');
            $table->string('nomor_batch');
            $table->date('tanggal_kadaluarsa');
            $table->integer('jumlah_kemasan');
            $table->integer('jumlah_unit');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_barang_detail');
        Schema::dropIfExists('penerimaan_barang');
    }
};