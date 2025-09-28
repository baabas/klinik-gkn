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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // [DIUBAH] Kolom untuk identitas Karyawan (bisa null jika non-karyawan)
            $table->string('nip', 30)->nullable()->unique();

            // [BARU] Kolom untuk identitas Non-Karyawan (bisa null jika karyawan)
            $table->string('nik', 16)->nullable()->unique();

            // Nama ini akan dipakai untuk semua user (pasien/dokter/dll)
            $table->string('nama_karyawan'); 

            // [DIUBAH] Email dan Password dibuat nullable untuk non-karyawan yang didaftarkan dokter
            $table->string('email')->nullable()->unique();
            $table->string('password')->nullable();

            $table->string('akses');
            $table->check("akses IN ('DOKTER', 'PENGADAAN', 'PASIEN')", 'users_akses_check');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};