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
        Schema::table('checkups', function (Blueprint $table) {
            // Tambahkan kolom suhu_badan setelah tinggi_badan
            $table->decimal('suhu_badan', 4, 1)->nullable()->after('tinggi_badan')->comment('Suhu badan dalam Celsius');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkups', function (Blueprint $table) {
            $table->dropColumn('suhu_badan');
        });
    }
};
