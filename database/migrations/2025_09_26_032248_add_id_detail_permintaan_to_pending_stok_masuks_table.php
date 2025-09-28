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
        Schema::table('pending_stok_masuks', function (Blueprint $table) {
            $table->unsignedBigInteger('id_detail_permintaan');
            $table->foreign('id_detail_permintaan')->references('id')->on('detail_permintaan_barang')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_stok_masuks', function (Blueprint $table) {
            $table->dropForeign(['id_detail_permintaan']);
            $table->dropColumn('id_detail_permintaan');
        });
    }
};
