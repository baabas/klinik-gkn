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
        // Hapus duplikasi yang ada terlebih dahulu (jika ada)
        // Set no_hp duplikat menjadi NULL, kecuali yang pertama
        DB::statement("
            UPDATE karyawan k1
            LEFT JOIN (
                SELECT no_hp, MIN(nip) as first_nip
                FROM karyawan
                WHERE no_hp IS NOT NULL AND no_hp != ''
                GROUP BY no_hp
                HAVING COUNT(*) > 1
            ) k2 ON k1.no_hp = k2.no_hp AND k1.nip != k2.first_nip
            SET k1.no_hp = NULL
            WHERE k2.no_hp IS NOT NULL
        ");

        DB::statement("
            UPDATE non_karyawan nk1
            LEFT JOIN (
                SELECT no_hp, MIN(nik) as first_nik
                FROM non_karyawan
                WHERE no_hp IS NOT NULL AND no_hp != ''
                GROUP BY no_hp
                HAVING COUNT(*) > 1
            ) nk2 ON nk1.no_hp = nk2.no_hp AND nk1.nik != nk2.first_nik
            SET nk1.no_hp = NULL
            WHERE nk2.no_hp IS NOT NULL
        ");

        // Tambahkan unique constraint untuk tabel karyawan
        if (Schema::hasColumn('karyawan', 'no_hp')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->unique('no_hp', 'karyawan_no_hp_unique');
            });
        }

        // Tambahkan unique constraint untuk tabel non_karyawan
        if (Schema::hasColumn('non_karyawan', 'no_hp')) {
            Schema::table('non_karyawan', function (Blueprint $table) {
                $table->unique('no_hp', 'non_karyawan_no_hp_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropUnique('karyawan_no_hp_unique');
        });

        Schema::table('non_karyawan', function (Blueprint $table) {
            $table->dropUnique('non_karyawan_no_hp_unique');
        });
    }
};
