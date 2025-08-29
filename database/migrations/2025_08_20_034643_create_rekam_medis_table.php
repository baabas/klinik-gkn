<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id('id_rekam_medis');

            // PERUBAHAN DI SINI:
            // Merujuk ke kolom 'id' di tabel 'users'
            $table->foreignId('id_pasien')->constrained('users')->onDelete('cascade');

            $table->string('nip', 30); // Ini NIP dokter
            $table->dateTime('tanggal_kunjungan');
            $table->string('riwayat_sakit')->nullable();
            $table->string('pengobatan')->nullable();
            $table->string('nama_sa')->nullable();
            $table->string('jenis_kelamin_sa', 10)->nullable();

            // Foreign key untuk NIP dokter ke tabel karyawan
            $table->foreign('nip')->references('nip')->on('karyawan')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
