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
            // Drop kolom komentar jika ada
            if (Schema::hasColumn('feedback_pasien', 'komentar')) {
                $table->dropColumn('komentar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_pasien', function (Blueprint $table) {
            $table->text('komentar')->nullable()->after('jumlah_obat_sesuai');
        });
    }
};
