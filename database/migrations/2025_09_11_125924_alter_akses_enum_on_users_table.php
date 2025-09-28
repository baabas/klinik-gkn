<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropCheck('users_akses_check');
            $table->check("akses IN ('DOKTER', 'PENGADAAN', 'PASIEN')", 'users_akses_check');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropCheck('users_akses_check');
            $table->check("akses IN ('DOKTER', 'PENGADAAN')", 'users_akses_check');
        });
    }
};
