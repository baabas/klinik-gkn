<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->string('kantor_saat_kunjungan', 100)->nullable()->after('id_lokasi');
            $table->string('lokasi_gedung_saat_kunjungan', 100)->nullable()->after('kantor_saat_kunjungan');
        });

        // Update data existing dengan kantor dari tabel karyawan
        DB::statement("
            UPDATE rekam_medis rm
            LEFT JOIN users u ON rm.NIP_pasien = u.nip
            LEFT JOIN karyawan k ON u.nip = k.nip
            SET rm.kantor_saat_kunjungan = k.kantor
            WHERE rm.NIP_pasien IS NOT NULL AND k.kantor IS NOT NULL
        ");

        // Update data existing dengan lokasi_gedung dari tabel non_karyawan
        DB::statement("
            UPDATE rekam_medis rm
            LEFT JOIN users u ON rm.NIK_pasien = u.nik
            LEFT JOIN non_karyawan nk ON u.nik = nk.nik
            SET rm.lokasi_gedung_saat_kunjungan = nk.lokasi_gedung
            WHERE rm.NIK_pasien IS NOT NULL AND nk.lokasi_gedung IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn(['kantor_saat_kunjungan', 'lokasi_gedung_saat_kunjungan']);
        });
    }
};
