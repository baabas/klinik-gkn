<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('obat', function (Blueprint $table) {
            $table->id('id_obat'); // Menggunakan id() untuk auto-increment primary key
            $table->string('kode_obat', 50)->unique()->nullable();
            $table->string('nama_obat')->unique();
            $table->string('satuan', 50)->nullable();
            $table->string('kemasan', 50)->nullable();
            $table->integer('stok_saat_ini')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
