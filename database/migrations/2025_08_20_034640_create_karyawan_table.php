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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->string('nip', 30)->primary();
            $table->string('nama_karyawan');
            $table->string('jabatan', 100)->nullable();
            $table->string('kantor', 100)->nullable();
            $table->string('email')->unique()->nullable();
            $table->text('alamat')->nullable();
            $table->string('agama', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->timestamps(); // Menambahkan created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
