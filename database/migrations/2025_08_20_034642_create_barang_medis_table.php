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
        Schema::create('barang_medis', function (Blueprint $table) {
            $table->id('id_obat');
            $table->string('kode_obat', 50)->unique();
            $table->string('nama_obat');
            $table->enum('tipe', ['OBAT', 'ALKES']);
            $table->string('satuan', 50);
            
            // [KOLOM BARU DITAMBAHKAN DI SINI]
            $table->string('kemasan', 100)->nullable();
            
            $table->string('kategori', 100)->nullable();
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