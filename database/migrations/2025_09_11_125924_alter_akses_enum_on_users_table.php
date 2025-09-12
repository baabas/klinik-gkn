<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY akses ENUM('DOKTER', 'PENGADAAN', 'PASIEN') NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY akses ENUM('DOKTER', 'PENGADAAN') NOT NULL
        ");
    }
};
