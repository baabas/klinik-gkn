<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarangMedis;
use App\Models\StokBarang;
use App\Models\LokasiKlinik;

class StokBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data barang medis
        $barangList = [
            [
                'kode_obat' => 'PCM500',
                'nama_obat' => 'Paracetamol 500mg',
                'tipe' => 'OBAT',
                'kemasan' => 'Box',
                'satuan' => 'Strip',
                'jumlah_satuan_perkemasan' => 10, // 1 Box = 10 Strip
                'jumlah_unit_persatuan' => 10,    // 1 Strip = 10 Tablet
                'satuan_terkecil' => 'Tablet',
                'min_stok' => 100,
            ],
            [
                'kode_obat' => 'AMOX500',
                'nama_obat' => 'Amoxicillin 500mg',
                'tipe' => 'OBAT',
                'kemasan' => 'Box',
                'satuan' => 'Strip',
                'jumlah_satuan_perkemasan' => 10, // 1 Box = 10 Strip
                'jumlah_unit_persatuan' => 10,    // 1 Strip = 10 Kapsul
                'satuan_terkecil' => 'Kapsul',
                'min_stok' => 50,
            ],
            [
                'kode_obat' => 'CTM4',
                'nama_obat' => 'Chlorpheniramine Maleate 4mg',
                'tipe' => 'OBAT',
                'kemasan' => 'Box',
                'satuan' => 'Strip',
                'jumlah_satuan_perkemasan' => 10, // 1 Box = 10 Strip
                'jumlah_unit_persatuan' => 12,    // 1 Strip = 12 Tablet
                'satuan_terkecil' => 'Tablet',
                'min_stok' => 120,
            ],
            [
                'kode_obat' => 'MASK-S',
                'nama_obat' => 'Masker Bedah',
                'tipe' => 'ALKES',
                'kemasan' => 'Box',
                'satuan' => 'Pack',
                'jumlah_satuan_perkemasan' => 20, // 1 Box = 20 Pack
                'jumlah_unit_persatuan' => 50,    // 1 Pack = 50 Piece
                'satuan_terkecil' => 'Piece',
                'min_stok' => 1000,
            ],
            [
                'kode_obat' => 'GLOVE-M',
                'nama_obat' => 'Sarung Tangan Medis M',
                'tipe' => 'ALKES',
                'kemasan' => 'Box',
                'satuan' => 'Pack',
                'jumlah_satuan_perkemasan' => 10, // 1 Box = 10 Pack
                'jumlah_unit_persatuan' => 100,   // 1 Pack = 100 Piece
                'satuan_terkecil' => 'Piece',
                'min_stok' => 500,
            ],
            [
                'kode_obat' => 'ALKOHOL',
                'nama_obat' => 'Alkohol 70%',
                'tipe' => 'ALKES',
                'kemasan' => 'Box',
                'satuan' => 'Bottle',
                'jumlah_satuan_perkemasan' => 12, // 1 Box = 12 Bottle
                'jumlah_unit_persatuan' => 1000,  // 1 Bottle = 1000 ml
                'satuan_terkecil' => 'ml',
                'min_stok' => 5000,
            ],
            [
                'kode_obat' => 'KASA-STR',
                'nama_obat' => 'Kasa Steril 16x16',
                'tipe' => 'ALKES',
                'kemasan' => 'Box',
                'satuan' => 'Pack',
                'jumlah_satuan_perkemasan' => 20, // 1 Box = 20 Pack
                'jumlah_unit_persatuan' => 12,    // 1 Pack = 12 Piece
                'satuan_terkecil' => 'Piece',
                'min_stok' => 240,
            ],
        ];

        // Insert atau update data barang
        foreach ($barangList as $barang) {
            BarangMedis::updateOrCreate(
                ['kode_obat' => $barang['kode_obat']], // Unique identifier
                $barang
            );
        }

        // Gunakan hanya lokasi dengan ID 1 dan 2
        $lokasiIds = [1, 2];
        
        // Dapatkan semua barang yang baru saja diinsert/update
        $barangMedis = BarangMedis::all();

        // Generate stok awal untuk setiap kombinasi barang dan lokasi
        foreach ($lokasiIds as $lokasiId) {
            foreach ($barangMedis as $barang) {
                // Generate random stok antara min_stok sampai min_stok * 3
                $stokAwal = rand($barang->min_stok, $barang->min_stok * 3);
                
                StokBarang::updateOrCreate(
                    [
                        'id_barang' => $barang->id_obat,
                        'id_lokasi' => $lokasiId,
                    ],
                    [
                        'jumlah' => $stokAwal,
                    ]
                );
            }
        }
    }
}