<?php

namespace Database\Seeders;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\StokBarang;
use Illuminate\Database\Seeder;

class BarangMedisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        StokBarang::truncate();
        BarangMedis::truncate();
        
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Data obat yang akan diisi
        $obatList = [
            [
                'kode_obat' => 'OBT001',
                'nama_obat' => 'Paracetamol 500mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Strip @10',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Tablet',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 50,
            ],
            [
                'kode_obat' => 'OBT002',
                'nama_obat' => 'Ibuprofen 400mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Strip @10',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Tablet',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 40,
            ],
            [
                'kode_obat' => 'OBT003',
                'nama_obat' => 'Amoxicillin 500mg',
                'satuan' => 'Kapsul',
                'kemasan' => 'Strip @10',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Kapsul',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Kapsul',
                'stok_minimal' => 30,
            ],
            [
                'kode_obat' => 'OBT004',
                'nama_obat' => 'Metformin 500mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Strip @10',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Tablet',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 60,
            ],
            [
                'kode_obat' => 'OBT005',
                'nama_obat' => 'Ciprofloxacin 500mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Strip @10',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Tablet',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 25,
            ],
            [
                'kode_obat' => 'OBT006',
                'nama_obat' => 'Omeprazole 20mg',
                'satuan' => 'Kapsul',
                'kemasan' => 'Strip @10',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Kapsul',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Kapsul',
                'stok_minimal' => 35,
            ],
            [
                'kode_obat' => 'OBT007',
                'nama_obat' => 'Loratadine 10mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Strip @10',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Tablet',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 40,
            ],
            [
                'kode_obat' => 'OBT008',
                'nama_obat' => 'Kasa Steril 10x10 cm',
                'satuan' => 'Pcs',
                'kemasan' => 'Pack',
                'kategori_barang' => 'BMHP',
                'isi_kemasan_jumlah' => 100,
                'isi_kemasan_satuan' => 'Pcs',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Pcs',
                'stok_minimal' => 20,
            ],
            [
                'kode_obat' => 'OBT009',
                'nama_obat' => 'Dexamethasone 0.5mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Strip @10',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Tablet',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 15,
            ],
            [
                'kode_obat' => 'OBT010',
                'nama_obat' => 'Masker Medis 3 Ply',
                'satuan' => 'Pcs',
                'kemasan' => 'Box',
                'kategori_barang' => 'APD',
                'isi_kemasan_jumlah' => 50,
                'isi_kemasan_satuan' => 'Pcs',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Pcs',
                'stok_minimal' => 100,
            ],
            [
                'kode_obat' => 'OBT011',
                'nama_obat' => 'Nebulizer Set',
                'satuan' => 'Set',
                'kemasan' => 'Box',
                'kategori_barang' => 'Alkes',
                'isi_kemasan_jumlah' => 1,
                'isi_kemasan_satuan' => 'Set',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Set',
                'stok_minimal' => 10,
            ],
            [
                'kode_obat' => 'OBT012',
                'nama_obat' => 'Sarung Tangan Latex',
                'satuan' => 'Pcs',
                'kemasan' => 'Box',
                'kategori_barang' => 'APD',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Pcs',
                'isi_per_satuan' => 1,
                'satuan_terkecil' => 'Pcs',
                'stok_minimal' => 30,
            ],
        ];

        // Insert data barang medis
        foreach ($obatList as $obat) {
            BarangMedis::create($obat);
        }

        $this->command->info('Data Barang Medis berhasil dibuat: ' . count($obatList) . ' item');

        // Buat stok untuk setiap obat di setiap lokasi
        $obatCreated = BarangMedis::all();
        $lokasi = LokasiKlinik::all();

        if ($lokasi->count() === 0) {
            $this->command->warn('Tidak ada lokasi klinik yang ditemukan. Lewati pembuatan stok.');
            return;
        }

        $stokPerObat = [
            'OBT001' => [24, 21], // total 45, critical
            'OBT002' => [20, 22], // total 42, critical
            'OBT004' => [32, 31], // total 63, near critical
        ];

        $stokCount = 0;
        foreach ($obatCreated as $obat) {
            foreach ($lokasi as $index => $loc) {
                $defaultStok = 120 + ($index * 15);
                $jumlahStok = $stokPerObat[$obat->kode_obat][$index] ?? $defaultStok;

                StokBarang::create([
                    'id_barang' => $obat->id_obat,
                    'id_lokasi' => $loc->id,
                    'jumlah' => $jumlahStok,
                ]);
                $stokCount++;
            }
        }

        $this->command->info('Data Stok Barang berhasil dibuat: ' . $stokCount . ' record');
    }
}
