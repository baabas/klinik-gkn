<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini menggabungkan semua perubahan pada tabel stok_histories:
     * - Create table original (2025_09_12_113406)
     * - Add details fields (2025_09_18_134654)
     * - Update null user_id (2025_09_23_135106)
     */
    public function up(): void
    {
        Schema::create('stok_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('barang_medis', 'id_obat')->onDelete('cascade');
            $table->foreignId('id_lokasi')->nullable()->constrained('lokasi_klinik')->nullOnDelete();
            
            // Detail transaksi
            $table->integer('perubahan');
            $table->integer('stok_sebelum')->nullable();
            $table->integer('stok_sesudah')->nullable();
            
            // User dan tanggal
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_transaksi')->nullable();
            
            // Detail kemasan
            $table->integer('jumlah_kemasan')->nullable();
            $table->integer('isi_per_kemasan')->nullable();
            $table->string('satuan_kemasan', 50)->nullable();
            $table->date('expired_at')->nullable();
            
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_histories');
    }
};