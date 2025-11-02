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
        Schema::create('feedback_pasien', function (Blueprint $table) {
            $table->id('id_feedback');
            $table->unsignedBigInteger('id_rekam_medis');
            $table->string('nip_pasien', 30)->nullable();
            $table->string('nik_pasien', 16)->nullable();
            $table->tinyInteger('rating')->comment('1=Sangat Tidak Puas, 2=Tidak Puas, 3=Cukup, 4=Puas, 5=Sangat Puas');
            $table->text('komentar')->nullable();
            $table->timestamp('waktu_feedback');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_rekam_medis')
                  ->references('id_rekam_medis')
                  ->on('rekam_medis')
                  ->onDelete('cascade');

            // Index untuk performa query
            $table->index('nip_pasien');
            $table->index('nik_pasien');
            $table->index('rating');
            $table->index('waktu_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_pasien');
    }
};
