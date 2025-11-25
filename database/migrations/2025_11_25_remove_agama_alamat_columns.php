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
        // Drop kolom alamat dan agama dari tabel karyawan
        if (Schema::hasTable('karyawan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                if (Schema::hasColumn('karyawan', 'alamat')) {
                    $table->dropColumn('alamat');
                }
                if (Schema::hasColumn('karyawan', 'agama')) {
                    $table->dropColumn('agama');
                }
            });
        }

        // Drop kolom alamat dan agama dari tabel non_karyawan
        if (Schema::hasTable('non_karyawan')) {
            Schema::table('non_karyawan', function (Blueprint $table) {
                if (Schema::hasColumn('non_karyawan', 'alamat')) {
                    $table->dropColumn('alamat');
                }
                if (Schema::hasColumn('non_karyawan', 'agama')) {
                    $table->dropColumn('agama');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore kolom alamat dan agama di tabel karyawan
        if (Schema::hasTable('karyawan')) {
            Schema::table('karyawan', function (Blueprint $table) {
                $table->text('alamat')->nullable()->after('kantor');
                $table->string('agama', 50)->nullable()->after('alamat');
            });
        }

        // Restore kolom alamat dan agama di tabel non_karyawan
        if (Schema::hasTable('non_karyawan')) {
            Schema::table('non_karyawan', function (Blueprint $table) {
                $table->text('alamat')->nullable()->after('nik');
                $table->string('agama', 50)->nullable()->after('alamat');
            });
        }
    }
};
