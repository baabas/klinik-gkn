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
        Schema::table('karyawan', function (Blueprint $table) {
            // Tambahkan kolom jenis_kelamin jika belum ada
            if (!Schema::hasColumn('karyawan', 'jenis_kelamin')) {
                $table->enum('jenis_kelamin', ['L', 'P'])->nullable()->after('tanggal_lahir')
                    ->comment('L = Laki-laki, P = Perempuan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            if (Schema::hasColumn('karyawan', 'jenis_kelamin')) {
                $table->dropColumn('jenis_kelamin');
            }
        });
    }
};
