<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_medis', function (Blueprint $table) {
            $table->integer('jumlah_satuan_perkemasan')->default(1)->comment('Jumlah satuan dalam 1 kemasan');
            $table->integer('jumlah_unit_persatuan')->default(1)->comment('Jumlah unit dalam 1 satuan');
            $table->string('satuan_terkecil')->nullable()->comment('Satuan terkecil (mis: tablet, kapsul)');
        });
    }

    public function down(): void
    {
        Schema::table('barang_medis', function (Blueprint $table) {
            $table->dropColumn(['jumlah_satuan_perkemasan', 'jumlah_unit_persatuan', 'satuan_terkecil']);
        });
    }
};