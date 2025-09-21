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
        Schema::create('non_karyawan', function (Blueprint $table) {
            // [DIUBAH] NIK sekarang menjadi Primary Key
            $table->string('nik', 16)->primary();
            
            // Kolom 'nama' dihapus karena akan diambil dari tabel 'users'
            
            $table->text('alamat')->nullable();
            $table->date('tanggal_lahir');
            $table->timestamps();

            // [BARU] Membuat relasi 1-to-1 ke tabel users
            $table->foreign('nik')->references('nik')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_karyawan');
    }
};