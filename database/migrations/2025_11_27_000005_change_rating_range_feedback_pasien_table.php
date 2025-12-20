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
        Schema::table('feedback_pasien', function (Blueprint $table) {
            // Ubah comment dan constraint untuk rating (1-3 saja)
            $table->tinyInteger('rating')
                ->change()
                ->comment('1=Tidak Puas, 2=Cukup, 3=Puas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback_pasien', function (Blueprint $table) {
            $table->tinyInteger('rating')
                ->change()
                ->comment('1=Sangat Tidak Puas, 2=Tidak Puas, 3=Cukup, 4=Puas, 5=Sangat Puas');
        });
    }
};
