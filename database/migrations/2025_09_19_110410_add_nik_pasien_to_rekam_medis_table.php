<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            // 1. Jadikan kolom nip_pasien nullable
            $table->string('nip_pasien', 30)->nullable()->change();

            // 2. Tambahkan kolom nik_pasien yang juga nullable
            $table->string('nik_pasien', 16)->nullable();

            // 3. Tambahkan foreign key constraint ke tabel non_karyawan
            $table->foreign('nik_pasien')->references('nik')->on('non_karyawan')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropForeign(['nik_pasien']);
            $table->dropColumn('nik_pasien');
            $table->string('nip_pasien', 30)->nullable(false)->change();
        });
    }
};
