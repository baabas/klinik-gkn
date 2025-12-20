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
        Schema::table('feedback_pasien', function (Blueprint $table) {
            // Tambahkan kolom jumlah_obat_sesuai jika belum ada
            if (!Schema::hasColumn('feedback_pasien', 'jumlah_obat_sesuai')) {
                $table->boolean('jumlah_obat_sesuai')->default(true)->after('rating');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_pasien', function (Blueprint $table) {
            if (Schema::hasColumn('feedback_pasien', 'jumlah_obat_sesuai')) {
                $table->dropColumn('jumlah_obat_sesuai');
            }
        });
    }
};
