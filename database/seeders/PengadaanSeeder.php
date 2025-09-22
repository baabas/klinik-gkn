<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LokasiKlinik;
use App\Models\BarangMedis;
use App\Models\StokBarang;

class PengadaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Menonaktifkan pengecekan foreign key untuk truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Kosongkan tabel terkait sebelum diisi
        StokBarang::truncate();
        BarangMedis::truncate();
        LokasiKlinik::truncate();
        
        // Mengaktifkan kembali pengecekan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat Lokasi Klinik
        $gkn1 = LokasiKlinik::create(['nama_lokasi' => 'Klinik GKN 1']);
        $gkn2 = LokasiKlinik::create(['nama_lokasi' => 'Klinik GKN 2']);
        $this->command->info('Lokasi klinik berhasil dibuat.');

        // 2. Buat Master Barang Medis (Obat & Alkes)
        $barang = [
            // Obat
            [
                'kode_obat' => 'OBT-0001',
                'nama_obat' => 'Paracetamol 500mg',
                'tipe' => 'OBAT',
                'satuan' => 'Strip',
                'kemasan' => 'Box',
                'jumlah_satuan_perkemasan' => 10, // 1 box = 10 strip
                'jumlah_unit_persatuan' => 10,    // 1 strip = 10 tablet
                'satuan_terkecil' => 'Tablet'
            ],
            [
                'kode_obat' => 'OBT-0002',
                'nama_obat' => 'Amoxicillin 500mg',
                'tipe' => 'OBAT',
                'satuan' => 'Strip',
                'kemasan' => 'Box',
                'jumlah_satuan_perkemasan' => 10, // 1 box = 10 strip
                'jumlah_unit_persatuan' => 10,    // 1 strip = 10 kapsul
                'satuan_terkecil' => 'Kapsul'
            ],
            [
                'kode_obat' => 'OBT-0003',
                'nama_obat' => 'Antasida Doen',
                'tipe' => 'OBAT',
                'satuan' => 'Strip',
                'kemasan' => 'Box',
                'jumlah_satuan_perkemasan' => 10, // 1 box = 10 strip
                'jumlah_unit_persatuan' => 10,    // 1 strip = 10 tablet
                'satuan_terkecil' => 'Tablet'
            ],
            // Alkes dengan konversi sederhana (1:1)
            [
                'kode_obat' => 'ALK-0001',
                'nama_obat' => 'Alkohol Swab',
                'tipe' => 'ALKES',
                'satuan' => 'Box',
                'kemasan' => 'Box',
                'jumlah_satuan_perkemasan' => 1,
                'jumlah_unit_persatuan' => 100,
                'satuan_terkecil' => 'Piece'
            ],
            [
                'kode_obat' => 'ALK-0002',
                'nama_obat' => 'Kasa Steril 16x16',
                'tipe' => 'ALKES',
                'satuan' => 'Box',
                'kemasan' => 'Box',
                'jumlah_satuan_perkemasan' => 1,
                'jumlah_unit_persatuan' => 10,
                'satuan_terkecil' => 'Piece'
            ]
        ];

        // Looping untuk membuat barang medis dan langsung mengisi stoknya
        foreach ($barang as $item) {
            $barangDibuat = BarangMedis::create($item);

            // 3. Isi Stok Awal untuk setiap barang di setiap klinik
            StokBarang::create(['id_barang' => $barangDibuat->id_obat, 'id_lokasi' => $gkn1->id, 'jumlah' => rand(50, 200)]);
            StokBarang::create(['id_barang' => $barangDibuat->id_obat, 'id_lokasi' => $gkn2->id, 'jumlah' => rand(30, 150)]);
        }
        $this->command->info('Master barang medis dan stok awal berhasil dibuat.');
    }
}