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
        Schema::create('surat_distribusi', function (Blueprint $table) {
            $table->id('id_surat');
            $table->string('nomor_surat', 50)->unique()->comment('Nomor surat unik untuk tracking');
            $table->string('kode_validasi', 20)->unique()->comment('Kode unik untuk QR');
            $table->unsignedBigInteger('id_lokasi_asal');
            $table->unsignedBigInteger('id_lokasi_tujuan');
            $table->unsignedBigInteger('id_user')->comment('User yang membuat surat');
            $table->date('tanggal_distribusi');
            $table->string('nomor_wa_validator', 20)->comment('Nomor WA pihak ketiga untuk notifikasi');
            $table->text('catatan')->nullable();
            $table->enum('status', ['pending', 'terkirim', 'diterima', 'divalidasi'])->default('pending');
            $table->timestamp('validated_at')->nullable();
            $table->string('validated_by')->nullable()->comment('Nama orang yang memvalidasi');
            $table->timestamps();

            $table->foreign('id_lokasi_asal')->references('id')->on('lokasi_klinik');
            $table->foreign('id_lokasi_tujuan')->references('id')->on('lokasi_klinik');
            $table->foreign('id_user')->references('id')->on('users');
        });

        // Tabel detail untuk item-item dalam surat distribusi
        Schema::create('detail_surat_distribusi', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_surat');
            $table->unsignedBigInteger('id_barang');
            $table->integer('jumlah')->comment('Jumlah dalam satuan terkecil');
            $table->timestamps();

            $table->foreign('id_surat')->references('id_surat')->on('surat_distribusi')->onDelete('cascade');
            $table->foreign('id_barang')->references('id_obat')->on('barang_medis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_surat_distribusi');
        Schema::dropIfExists('surat_distribusi');
    }
};