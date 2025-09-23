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
            ['kode_obat' => 'OBT-0001', 'nama_obat' => 'Paracetamol 500mg', 'tipe' => 'OBAT', 'satuan_dasar' => 'tablet', 'kemasan' => 'Strip'],
            ['kode_obat' => 'OBT-0002', 'nama_obat' => 'Amoxicillin 500mg', 'tipe' => 'OBAT', 'satuan_dasar' => 'kapsul', 'kemasan' => 'Strip'],
            ['kode_obat' => 'OBT-0003', 'nama_obat' => 'Antasida Doen', 'tipe' => 'OBAT', 'satuan_dasar' => 'tablet', 'kemasan' => 'Botol'],
            ['kode_obat' => 'OBT-0004', 'nama_obat' => 'OBH Combi Batuk', 'tipe' => 'OBAT', 'satuan_dasar' => 'pcs', 'kemasan' => 'Botol 100ml'],
            ['kode_obat' => 'OBT-0005', 'nama_obat' => 'Loratadine 10mg', 'tipe' => 'OBAT', 'satuan_dasar' => 'tablet', 'kemasan' => 'Strip'],
            // Alkes
            ['kode_obat' => 'ALK-0001', 'nama_obat' => 'Alkohol Swab', 'tipe' => 'ALKES', 'satuan_dasar' => 'pcs', 'kemasan' => 'Box isi 100'],
            ['kode_obat' => 'ALK-0002', 'nama_obat' => 'Kasa Steril 16x16', 'tipe' => 'ALKES', 'satuan_dasar' => 'pcs', 'kemasan' => 'Box isi 10'],
            ['kode_obat' => 'ALK-0003', 'nama_obat' => 'Plester Rol Kain', 'tipe' => 'ALKES', 'satuan_dasar' => 'pcs', 'kemasan' => 'Rol 1.25cm x 1m'],
            ['kode_obat' => 'ALK-0004', 'nama_obat' => 'Sarung Tangan Medis (M)', 'tipe' => 'ALKES', 'satuan_dasar' => 'pcs', 'kemasan' => 'Box isi 100'],
            ['kode_obat' => 'ALK-0005', 'nama_obat' => 'Masker Medis 3-ply', 'tipe' => 'ALKES', 'satuan_dasar' => 'pcs', 'kemasan' => 'Box isi 50'],
        ];

        // Looping untuk membuat barang medis dan langsung mengisi stoknya
        foreach ($barang as $item) {
            $barangDibuat = BarangMedis::create($item);

            // 3. Isi Stok Awal untuk setiap barang di setiap klinik
            $stokGkn1 = rand(50, 200);
            $stokGkn2 = rand(30, 150);

            StokBarang::create(['id_barang' => $barangDibuat->id_obat, 'id_lokasi' => $gkn1->id, 'jumlah' => $stokGkn1]);
            StokBarang::create(['id_barang' => $barangDibuat->id_obat, 'id_lokasi' => $gkn2->id, 'jumlah' => $stokGkn2]);

            $barangDibuat->update(['stok' => $stokGkn1 + $stokGkn2]);

            if (!empty($item['kemasan'])) {
                $isiPerKemasan = 1;
                if (preg_match('/(\d+)/', $item['kemasan'], $matches)) {
                    $isiPerKemasan = (int) $matches[1];
                }

                $barangDibuat->kemasanBarang()->create([
                    'nama_kemasan' => $item['kemasan'],
                    'isi_per_kemasan' => max(1, $isiPerKemasan),
                    'is_default' => true,
                ]);
            }
        }
        $this->command->info('Master barang medis dan stok awal berhasil dibuat.');
    }
}
