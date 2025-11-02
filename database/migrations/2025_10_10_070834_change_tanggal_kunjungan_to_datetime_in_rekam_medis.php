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
            // Ubah dari DATE ke DATETIME dan set default ke created_at atau current time
            $table->dateTime('tanggal_kunjungan')->change();
        });
        
        // Update existing data: gunakan created_at jika ada, atau tambahkan waktu sekarang
        DB::statement('
            UPDATE rekam_medis 
            SET tanggal_kunjungan = COALESCE(created_at, CONCAT(tanggal_kunjungan, " ", CURTIME()))
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            // Kembalikan ke DATE
            $table->date('tanggal_kunjungan')->change();
        });
    }
};
