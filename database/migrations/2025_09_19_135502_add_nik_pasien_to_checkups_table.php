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
        Schema::table('checkups', function (Blueprint $table) {
            // 1. Jadikan kolom nip_pasien yang sudah ada menjadi nullable
            $table->string('nip_pasien', 30)->nullable()->change();

            // 2. Tambahkan kolom baru nik_pasien yang juga nullable
            $table->string('nik_pasien', 16)->nullable();

            // 3. Tambahkan foreign key ke tabel users untuk NIK
            $table->foreign('nik_pasien')->references('nik')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkups', function (Blueprint $table) {
            $table->dropForeign(['nik_pasien']);
            $table->dropColumn('nik_pasien');
            $table->string('nip_pasien', 30)->nullable(false)->change();
        });
    }
};
