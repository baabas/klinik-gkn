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
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Kolom untuk menandai feedback yang sudah expired/ditimpa oleh pasien baru
            $table->boolean('feedback_expired')->default(false)->after('id_lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn('feedback_expired');
        });
    }
};
