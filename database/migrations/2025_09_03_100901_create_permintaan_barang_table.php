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
        Schema::create('permintaan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_permintaan', 50)->unique();
            $table->foreignId('id_lokasi_peminta')->constrained('lokasi_klinik')->onDelete('restrict');
            $table->foreignId('id_user_peminta')->constrained('users')->onDelete('restrict');
            $table->date('tanggal_permintaan');
            $table->text('catatan')->nullable();
            $table->string('status')->default('PENDING');
            $table->check("status IN ('PENDING', 'APPROVED', 'REJECTED', 'PROCESSING', 'COMPLETED')", 'permintaan_barang_status_check');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_barang');
    }
};
