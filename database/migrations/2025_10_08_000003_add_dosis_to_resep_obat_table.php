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
        Schema::table('resep_obat', function (Blueprint $table) {
            // Tambahkan kolom dosis setelah jumlah
            $table->string('dosis', 255)->nullable()->after('jumlah')->comment('Dosis obat, contoh: "3x1", "2x1 setelah makan"');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resep_obat', function (Blueprint $table) {
            $table->dropColumn('dosis');
        });
    }
};
