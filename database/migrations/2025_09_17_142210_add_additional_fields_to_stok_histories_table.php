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
            $table->foreignId('user_id')->nullable()->after('keterangan')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_histories', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['stok_sebelum', 'stok_sesudah', 'user_id']);
        });
    }
};
