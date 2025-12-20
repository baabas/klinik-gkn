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
        // Migration ini tidak lagi digunakan
        // Kolom tanggal_lahir dan jenis_kelamin hanya ada di tabel karyawan
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak ada yang di-rollback
    }
};
