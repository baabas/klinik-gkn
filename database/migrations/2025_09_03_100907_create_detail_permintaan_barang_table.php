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
    Schema::create('detail_permintaan_barang', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_permintaan')->constrained('permintaan_barang')->onDelete('cascade');
        $table->foreignId('id_barang')->nullable()->constrained('barang_medis', 'id_obat')->onDelete('restrict');
        $table->integer('jumlah_diminta');
        $table->integer('jumlah_disetujui')->nullable();
        $table->string('nama_barang_baru')->nullable();
        $table->enum('tipe_barang_baru', ['OBAT', 'ALKES'])->nullable();
        $table->string('satuan_barang_baru', 100)->nullable();
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
