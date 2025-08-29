<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_obat', function (Blueprint $table) {
            $table->id('id_resep');
            $table->foreignId('id_rekam_medis')->constrained('rekam_medis', 'id_rekam_medis')->onDelete('cascade');
            $table->foreignId('id_obat')->constrained('obat', 'id_obat')->onDelete('cascade');
            $table->integer('kuantitas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_obat');
    }
};
