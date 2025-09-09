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
        Schema::create('checkups', function (Blueprint $table) {
            $table->id();
            // Foreign key ke tabel users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->date('tanggal_pemeriksaan');

            // Kolom PEMERIKSAAN (semua bisa kosong)
            $table->string('tekanan_darah', 20)->nullable();
            $table->string('gula_darah', 20)->nullable();
            $table->string('kolesterol', 20)->nullable();
            $table->string('asam_urat', 20)->nullable();

            // Kolom PENGUKURAN (semua bisa kosong)
            $table->string('berat_badan', 20)->nullable();
            $table->string('tinggi_badan', 20)->nullable();
            $table->string('indeks_massa_tubuh', 20)->nullable();
            $table->string('lingkar_perut', 20)->nullable();

            // Kolom opsional untuk Suami/Anak (SA)
            $table->string('nama_sa')->nullable();
            $table->string('jenis_kelamin_sa', 20)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkups');
    }
};