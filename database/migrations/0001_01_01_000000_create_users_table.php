<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
// ...
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('nip', 30)->unique();
    $table->string('nama_karyawan');
    $table->string('email')->unique();
    $table->string('password');
     $table->string('akses')->default('PASIEN');
    $table->rememberToken();
    $table->timestamps();
});
// ...
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
