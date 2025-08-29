<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_penyakit', function (Blueprint $table) {
            $table->string('kode_penyakit', 20)->primary();
            $table->string('nama_penyakit')->unique();
            // Di Laravel, kita tidak perlu mendefinisikan timestamps jika tidak dibutuhkan.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_penyakit');
    }
};
