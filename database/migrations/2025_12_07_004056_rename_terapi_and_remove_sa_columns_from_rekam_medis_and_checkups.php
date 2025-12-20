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
        // Rename terapi to treatment in rekam_medis table
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->renameColumn('terapi', 'treatment');
            $table->dropColumn(['nama_sa', 'jenis_kelamin_sa']);
        });

        // Remove nama_sa and jenis_kelamin_sa from checkups table
        Schema::table('checkups', function (Blueprint $table) {
            $table->dropColumn(['nama_sa', 'jenis_kelamin_sa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert treatment to terapi in rekam_medis table
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->renameColumn('treatment', 'terapi');
            $table->string('nama_sa', 100)->nullable()->after('id_rekam_medis');
            $table->enum('jenis_kelamin_sa', ['Laki-laki', 'Perempuan'])->nullable()->after('nama_sa');
        });

        // Restore nama_sa and jenis_kelamin_sa in checkups table
        Schema::table('checkups', function (Blueprint $table) {
            $table->string('nama_sa', 100)->nullable()->after('id_checkup');
            $table->enum('jenis_kelamin_sa', ['Laki-laki', 'Perempuan'])->nullable()->after('nama_sa');
        });
    }
};
