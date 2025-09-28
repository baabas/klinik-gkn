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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_lokasi')->nullable();

            $table->foreign('id_lokasi')->references('id')->on('lokasi_klinik')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key dulu sebelum drop kolom
            $table->dropForeign(['id_lokasi']);
            $table->dropColumn('id_lokasi');
        });
    }
};
