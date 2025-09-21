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
        if (Schema::hasTable('permintaan_barang_detail')) {
            Schema::drop('permintaan_barang_detail');
        }

        if (Schema::hasTable('detail_permintaan_barang')) {
            Schema::drop('detail_permintaan_barang');
        }

        if (Schema::hasTable('permintaan_barang')) {
            Schema::drop('permintaan_barang');
        }

        Schema::create('permintaan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->date('tanggal');
            $table->foreignId('peminta_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasi_klinik')->restrictOnDelete();
            $table->enum('status', ['DRAFT', 'DIAJUKAN', 'DISETUJUI', 'DITOLAK', 'DIPENUHI'])->default('DRAFT');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('permintaan_barang_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permintaan_id')->constrained('permintaan_barang')->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained('barang_medis', 'id_obat')->nullOnDelete();
            $table->foreignId('barang_kemasan_id')->nullable()->constrained('barang_kemasan')->nullOnDelete();
            $table->string('nama_barang_baru')->nullable();
            $table->decimal('jumlah', 12, 2);
            $table->unsignedBigInteger('total_unit')->nullable();
            $table->string('satuan', 50)->nullable();
            $table->string('kemasan', 150)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_barang_detail');
        Schema::dropIfExists('permintaan_barang');

        Schema::create('permintaan_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_permintaan', 50)->unique();
            $table->foreignId('id_lokasi_peminta')->constrained('lokasi_klinik')->onDelete('restrict');
            $table->foreignId('id_user_peminta')->constrained('users')->onDelete('restrict');
            $table->date('tanggal_permintaan');
            $table->text('catatan')->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'PROCESSING', 'COMPLETED'])->default('PENDING');
            $table->timestamps();
        });

        Schema::create('detail_permintaan_barang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_permintaan')->constrained('permintaan_barang')->onDelete('cascade');
            $table->foreignId('id_barang')->nullable()->constrained('barang_medis', 'id_obat')->onDelete('restrict');
            $table->integer('jumlah_diminta');
            $table->integer('jumlah_disetujui')->nullable();
            $table->string('nama_barang_baru')->nullable();
            $table->enum('tipe_barang_baru', ['OBAT', 'ALKES'])->nullable();
            $table->string('satuan_barang_baru', 100)->nullable();
            $table->timestamps();

            $table->unique(['id_permintaan', 'id_barang']);
        });
    }
};
