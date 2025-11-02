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
            // Tambahkan kolom id_lokasi setelah id_dokter
            $table->unsignedBigInteger('id_lokasi')->nullable()->after('id_dokter');
            
            // Foreign key ke tabel lokasi_klinik (primary key: id, bukan id_lokasi)
            $table->foreign('id_lokasi')
                  ->references('id')
                  ->on('lokasi_klinik')
                  ->onDelete('set null');
            
            // Index untuk performa query
            $table->index('id_lokasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Drop foreign key dan index terlebih dahulu
            $table->dropForeign(['id_lokasi']);
            $table->dropIndex(['id_lokasi']);
            
            // Drop kolom id_lokasi
            $table->dropColumn('id_lokasi');
        });
    }
};
