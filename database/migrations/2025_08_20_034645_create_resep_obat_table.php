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
        Schema::create('resep_obat', function (Blueprint $table) {
            $table->id('id_resep_obat');

            // Kolom foreign key ke tabel rekam_medis
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');

            // --- PERBAIKAN DI SINI ---
            // Arahkan foreign key ke tabel 'barang_medis' dengan primary key 'id_obat'
            $table->foreignId('id_obat')->constrained('barang_medis', 'id_obat')->onDelete('cascade');

            $table->integer('kuantitas');
            $table->string('dosis');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_obat');
    }
};
