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
        Schema::table('resep_obat', function (Blueprint $table) {
            // Menghapus kolom 'dosis'
            $table->dropColumn('dosis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resep_obat', function (Blueprint $table) {
            // Membuat kembali kolom 'dosis' jika migrasi di-rollback
            $table->string('dosis')->nullable()->after('kuantitas');
        });
    }
};