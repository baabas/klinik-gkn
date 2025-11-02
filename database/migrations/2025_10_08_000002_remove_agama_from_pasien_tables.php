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
        // Drop kolom agama dari tabel karyawan
        if (Schema::hasColumn('karyawan', 'agama')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->dropColumn('agama');
            });
        }

        // Drop kolom agama dari tabel non_karyawan
        if (Schema::hasColumn('non_karyawan', 'agama')) {
            Schema::table('non_karyawan', function (Blueprint $table) {
                $table->dropColumn('agama');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore kolom agama di tabel karyawan
        Schema::table('karyawan', function (Blueprint $table) {
            $table->string('agama', 50)->nullable()->after('jenis_kelamin');
        });

        // Restore kolom agama di tabel non_karyawan
        Schema::table('non_karyawan', function (Blueprint $table) {
            $table->string('agama', 50)->nullable()->after('jenis_kelamin');
        });
    }
};
