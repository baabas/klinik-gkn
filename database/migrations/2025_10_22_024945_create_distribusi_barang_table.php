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
        Schema::create('distribusi_barang', function (Blueprint $table) {
            $table->id('id_distribusi');
            $table->unsignedBigInteger('id_barang')->comment('FK ke barang_medis');
            $table->unsignedBigInteger('id_lokasi_asal')->comment('FK ke lokasi_klinik (asal)');
            $table->unsignedBigInteger('id_lokasi_tujuan')->comment('FK ke lokasi_klinik (tujuan)');
            $table->unsignedBigInteger('id_user')->comment('FK ke users (yang melakukan distribusi)');
            $table->integer('jumlah')->comment('Jumlah barang yang didistribusikan (satuan terkecil)');
            $table->text('keterangan')->nullable()->comment('Keterangan distribusi');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->comment('Status validasi');
            $table->unsignedBigInteger('validated_by')->nullable()->comment('FK ke users (pengadaan yang validasi)');
            $table->timestamp('validated_at')->nullable()->comment('Waktu validasi');
            $table->text('validation_note')->nullable()->comment('Catatan validasi dari pengadaan');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_barang')->references('id_obat')->on('barang_medis')->onDelete('cascade');
            $table->foreign('id_lokasi_asal')->references('id')->on('lokasi_klinik')->onDelete('cascade');
            $table->foreign('id_lokasi_tujuan')->references('id')->on('lokasi_klinik')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('id_barang');
            $table->index('id_lokasi_asal');
            $table->index('id_lokasi_tujuan');
            $table->index('id_user');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_barang');
    }
};
