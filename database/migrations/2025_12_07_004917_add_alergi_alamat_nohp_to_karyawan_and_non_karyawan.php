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
        Schema::table('karyawan', function (Blueprint $table) {
            $table->text('alergi')->nullable()->after('jenis_kelamin');
            $table->string('no_hp', 20)->nullable()->after('email');
        });

        Schema::table('non_karyawan', function (Blueprint $table) {
            $table->text('alergi')->nullable()->after('jenis_kelamin');
            $table->string('no_hp', 20)->nullable()->after('tanggal_lahir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawan', function (Blueprint $table) {
            $table->dropColumn(['alergi', 'no_hp']);
        });

        Schema::table('non_karyawan', function (Blueprint $table) {
            $table->dropColumn(['alergi', 'no_hp']);
        });
    }
};
