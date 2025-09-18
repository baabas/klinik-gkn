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
        Schema::table('stok_histories', function (Blueprint $table) {
            $table->integer('stok_sebelum')->nullable()->after('perubahan');
            $table->integer('stok_sesudah')->nullable()->after('stok_sebelum');
            $table->foreignId('user_id')->nullable()->after('stok_sesudah')->constrained('users')->nullOnDelete();
            $table->date('tanggal_transaksi')->nullable()->after('user_id');
            $table->integer('jumlah_kemasan')->nullable()->after('tanggal_transaksi');
            $table->integer('isi_per_kemasan')->nullable()->after('jumlah_kemasan');
            $table->string('satuan_kemasan', 50)->nullable()->after('isi_per_kemasan');
            $table->date('expired_at')->nullable()->after('satuan_kemasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_histories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'stok_sebelum',
                'stok_sesudah',
                'user_id',
                'tanggal_transaksi',
                'jumlah_kemasan',
                'isi_per_kemasan',
                'satuan_kemasan',
                'expired_at',
            ]);
        });
    }
};
