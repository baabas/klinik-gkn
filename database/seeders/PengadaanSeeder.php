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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Kosongkan tabel sebelum diisi
        LokasiKlinik::truncate();
        BarangMedis::truncate();
        StokBarang::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat Lokasi Klinik
        $gudang = LokasiKlinik::create(['nama_lokasi' => 'Gudang Pusat']);
        $gkn1 = LokasiKlinik::create(['nama_lokasi' => 'Klinik GKN 1']);
        $gkn2 = LokasiKlinik::create(['nama_lokasi' => 'Klinik GKN 2']);

        // 2. Buat Barang Medis (Obat & Alkes)
        $barang = [
            // Obat
            ['kode_obat' => 'OBT-0001', 'nama_obat' => 'Paracetamol 500mg', 'tipe' => 'OBAT', 'satuan' => 'Tablet', 'kemasan' => 'Strip'],
            ['kode_obat' => 'OBT-0002', 'nama_obat' => 'Amoxicillin 500mg', 'tipe' => 'OBAT', 'satuan' => 'Kapsul', 'kemasan' => 'Strip'],
            ['kode_obat' => 'OBT-0003', 'nama_obat' => 'Antasida Doen', 'tipe' => 'OBAT', 'satuan' => 'Tablet', 'kemasan' => 'Botol'],
            ['kode_obat' => 'OBT-0004', 'nama_obat' => 'OBH Combi Batuk', 'tipe' => 'OBAT', 'satuan' => 'Botol', 'kemasan' => 'Botol 100ml'],
            ['kode_obat' => 'OBT-0005', 'nama_obat' => 'Loratadine 10mg', 'tipe' => 'OBAT', 'satuan' => 'Tablet', 'kemasan' => 'Strip'],
            // Alkes
            ['kode_obat' => 'ALK-0001', 'nama_obat' => 'Alkohol Swab', 'tipe' => 'ALKES', 'satuan' => 'Box', 'kemasan' => 'Box isi 100'],
            ['kode_obat' => 'ALK-0002', 'nama_obat' => 'Kasa Steril 16x16', 'tipe' => 'ALKES', 'satuan' => 'Box', 'kemasan' => 'Box isi 10'],
            ['kode_obat' => 'ALK-0003', 'nama_obat' => 'Plester Rol Kain', 'tipe' => 'ALKES', 'satuan' => 'Rol', 'kemasan' => 'Rol 1.25cm x 1m'],
            ['kode_obat' => 'ALK-0004', 'nama_obat' => 'Sarung Tangan Medis (M)', 'tipe' => 'ALKES', 'satuan' => 'Box', 'kemasan' => 'Box isi 100'],
            ['kode_obat' => 'ALK-0005', 'nama_obat' => 'Masker Medis 3-ply', 'tipe' => 'ALKES', 'satuan' => 'Box', 'kemasan' => 'Box isi 50'],
            ['kode_obat' => 'ALK-0006', 'nama_obat' => 'Alat Tes Gula Darah', 'tipe' => 'ALKES', 'satuan' => 'Pcs', 'kemasan' => 'Set'],
            ['kode_obat' => 'ALK-0007', 'nama_obat' => 'Strip Tes Gula Darah', 'tipe' => 'ALKES', 'satuan' => 'Box', 'kemasan' => 'Box isi 25'],
            ['kode_obat' => 'ALK-0008', 'nama_obat' => 'Lancet Jarum', 'tipe' => 'ALKES', 'satuan' => 'Box', 'kemasan' => 'Box isi 100'],
            ['kode_obat' => 'ALK-0009', 'nama_obat' => 'Termometer Digital', 'tipe' => 'ALKES', 'satuan' => 'Pcs', 'kemasan' => 'Pcs'],
            ['kode_obat' => 'ALK-0010', 'nama_obat' => 'Betadine Antiseptic 15ml', 'tipe' => 'ALKES', 'satuan' => 'Botol', 'kemasan' => 'Botol'],
        ];

        foreach ($barang as $item) {
            $barangDibuat = BarangMedis::create($item);

            // 3. Isi Stok Awal untuk setiap barang
            StokBarang::create(['id_barang' => $barangDibuat->id_obat, 'id_lokasi' => $gudang->id, 'jumlah' => rand(500, 1000)]);
            StokBarang::create(['id_barang' => $barangDibuat->id_obat, 'id_lokasi' => $gkn1->id, 'jumlah' => rand(50, 200)]);
            StokBarang::create(['id_barang' => $barangDibuat->id_obat, 'id_lokasi' => $gkn2->id, 'jumlah' => rand(30, 150)]);
        }
    }
}
