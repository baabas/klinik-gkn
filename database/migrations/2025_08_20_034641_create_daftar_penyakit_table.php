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
        Schema::create('daftar_penyakit', function (Blueprint $table) {
            // 1. Mengganti nama kolom primary key menjadi ICD10
            $table->string('ICD10', 20)->primary();
            
            // 2. Menghapus unique constraint untuk fleksibilitas data impor
            $table->string('nama_penyakit');
            
            // 3. Menambahkan timestamps (created_at dan updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_penyakit');
    }
};