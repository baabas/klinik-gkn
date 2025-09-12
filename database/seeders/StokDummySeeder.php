<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LokasiKlinik;
use App\Models\BarangMedis;
use App\Models\StokBarang;

class StokDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gkn1 = LokasiKlinik::firstOrCreate(['nama_lokasi' => 'GKN 1']);
        $gkn2 = LokasiKlinik::firstOrCreate(['nama_lokasi' => 'GKN 2']);

        $items = [
            ['kode_obat' => 'OBT-1001', 'nama_obat' => 'Vitamin C 500mg', 'tipe' => 'OBAT', 'satuan' => 'Tablet', 'kemasan' => 'Strip'],
            ['kode_obat' => 'ALK-1001', 'nama_obat' => 'Termometer Digital', 'tipe' => 'ALKES', 'satuan' => 'Unit', 'kemasan' => 'Pcs'],
        ];

        foreach ($items as $item) {
            $barang = BarangMedis::firstOrCreate(
                ['kode_obat' => $item['kode_obat']],
                $item
            );

            StokBarang::updateOrCreate(
                ['id_barang' => $barang->id_obat, 'id_lokasi' => $gkn1->id],
                ['jumlah' => rand(10, 50)]
            );

            StokBarang::updateOrCreate(
                ['id_barang' => $barang->id_obat, 'id_lokasi' => $gkn2->id],
                ['jumlah' => rand(10, 50)]
            );
        }
    }
}
