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
        Schema::create('detail_diagnosa', function (Blueprint $table) {
            $table->id('id_detail_diagnosa');
            $table->unsignedBigInteger('id_rekam_medis');
            
            // Mengubah nama kolom foreign key agar konsisten
            $table->string('ICD10', 20); 
            $table->timestamps();

            $table->foreign('id_rekam_medis')->references('id_rekam_medis')->on('rekam_medis')->onDelete('cascade');
            
            // Mengubah referensi foreign key ke kolom ICD10 di tabel daftar_penyakit
            $table->foreign('ICD10')->references('ICD10')->on('daftar_penyakit')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_diagnosa');
    }
};