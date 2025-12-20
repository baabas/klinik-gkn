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
        // Cek apakah kolom alamat sudah ada sebelum menambahkan
        Schema::table('karyawan', function (Blueprint $table) {
            if (!Schema::hasColumn('karyawan', 'alamat')) {
                $table->text('alamat')->nullable()->after('email');
            }
        });

        Schema::table('non_karyawan', function (Blueprint $table) {
            if (!Schema::hasColumn('non_karyawan', 'alamat')) {
                $table->text('alamat')->nullable()->after('nik');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            if (Schema::hasColumn('karyawan', 'alamat')) {
                $table->dropColumn('alamat');
            }
        });

        Schema::table('non_karyawan', function (Blueprint $table) {
            if (Schema::hasColumn('non_karyawan', 'alamat')) {
                $table->dropColumn('alamat');
            }
        });
    }
};
