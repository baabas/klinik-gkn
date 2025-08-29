<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_diagnosa', function (Blueprint $table) {
            $table->id('id_detail_diagnosa');
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->string('kode_penyakit', 20);

            // Foreign key constraint to daftar_penyakit table
            $table->foreign('kode_penyakit')->references('kode_penyakit')->on('daftar_penyakit')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_diagnosa');
    }
};
