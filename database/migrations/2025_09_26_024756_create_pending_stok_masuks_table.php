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
        Schema::create('pending_stok_masuks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_permintaan'); // Referensi ke permintaan_barang
            $table->unsignedBigInteger('id_barang'); // Referensi ke barang_medis
            $table->unsignedBigInteger('id_lokasi'); // Lokasi tujuan barang
            $table->integer('jumlah_kemasan'); // Jumlah kemasan yang masuk
            $table->integer('isi_per_kemasan'); // Isi per kemasan saat input
            $table->string('satuan_kemasan', 50)->default('Box'); // Satuan kemasan
            $table->date('tanggal_masuk'); // Tanggal masuk
            $table->date('expired_at')->nullable(); // Tanggal expired
            $table->text('keterangan')->nullable(); // Keterangan batch
            $table->unsignedBigInteger('user_id'); // User yang input (pengadaan)
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_permintaan')->references('id')->on('permintaan_barang')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_obat')->on('barang_medis')->onDelete('cascade');
            $table->foreign('id_lokasi')->references('id')->on('lokasi_klinik')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index untuk performance
            $table->index(['id_permintaan', 'id_barang']);
            $table->index('id_lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pending_stok_masuks');
    }
};
