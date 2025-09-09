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
    Schema::create('stok_barang', function (Blueprint $table) {
        $table->id();
        // Pastikan nama foreignId sesuai dengan nama primary key di tabel induk
        $table->foreignId('id_barang')->constrained('barang_medis', 'id_obat')->onDelete('cascade');
        $table->foreignId('id_lokasi')->constrained('lokasi_klinik')->onDelete('cascade');
        $table->integer('jumlah')->default(0);
        $table->timestamps();

        // Index unik untuk mencegah duplikasi
        $table->unique(['id_barang', 'id_lokasi']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_barang');
    }
};
