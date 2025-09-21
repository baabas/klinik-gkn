// File: 2025_08_20_034643_create_rekam_medis_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id('id_rekam_medis');
            $table->string('nip_pasien', 30); // Biarkan seperti ini dulu
            $table->unsignedBigInteger('id_dokter');
            $table->date('tanggal_kunjungan');
            $table->text('anamnesa')->nullable();
            $table->text('terapi')->nullable();
            $table->string('nama_sa')->nullable();
            $table->string('jenis_kelamin_sa')->nullable();
            $table->timestamps();

            $table->foreign('nip_pasien')->references('nip')->on('karyawan')->onDelete('cascade');
            $table->foreign('id_dokter')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};