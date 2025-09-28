<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\LokasiKlinik;

class PengadaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menonaktifkan pengecekan foreign key untuk truncate
        Schema::disableForeignKeyConstraints();

        // Kosongkan tabel lokasi sebelum diisi
        LokasiKlinik::truncate();

        // Mengaktifkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();

        // 1. Buat Lokasi Klinik
        $gkn1 = LokasiKlinik::create(['nama_lokasi' => 'Klinik GKN 1']);
        $gkn2 = LokasiKlinik::create(['nama_lokasi' => 'Klinik GKN 2']);
        $this->command->info('Lokasi klinik berhasil dibuat.');

        // Note: Barang medis akan diisi manual melalui web interface
        $this->command->info('Seeder selesai. Barang medis dapat diisi manual melalui web.');
    }
}
