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
        Schema::table('barang_medis', function (Blueprint $table) {
            $table->unsignedInteger('isi_per_kemasan')->nullable()->after('satuan');
            $table->string('satuan_kemasan', 50)->nullable()->after('isi_per_kemasan');
            $table->string('satuan_terkecil', 50)->nullable()->after('satuan_kemasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_medis', function (Blueprint $table) {
            $table->dropColumn(['isi_per_kemasan', 'satuan_kemasan', 'satuan_terkecil']);
        });
    }
};
