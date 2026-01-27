<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarangMedis;
use App\Models\StokBarang;
use App\Models\LokasiKlinik;

class BarangMedisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini membuat 10 obat dengan variasi stok:
     * - Beberapa di bawah stok minimal (kritis)
     * - Beberapa mendekati stok minimal (menipis)
     * - Beberapa di atas stok minimal (aman)
     */
    public function run(): void
    {
        // Ambil lokasi klinik pertama (GKN 1)
        $lokasi = LokasiKlinik::first();
        
        if (!$lokasi) {
            $this->command->error('Lokasi klinik tidak ditemukan. Jalankan PengadaanSeeder terlebih dahulu.');
            return;
        }

        $obatList = [
            // 1. STOK KRITIS - Di bawah minimal
            [
                'kode_obat' => 'MED-001',
                'nama_obat' => 'Paracetamol 500mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Box',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10, // 10 strip per box
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10, // 10 tablet per strip
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 200, // Minimal 200 tablet
                'stok_actual' => 120, // Stok 120 tablet (KRITIS - di bawah minimal)
            ],
            
            // 2. STOK KRITIS - Di bawah minimal
            [
                'kode_obat' => 'MED-002',
                'nama_obat' => 'Amoxicillin 500mg',
                'satuan' => 'Kaplet',
                'kemasan' => 'Box',
                'kategori_barang' => 'Antibiotik',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10,
                'satuan_terkecil' => 'Kaplet',
                'stok_minimal' => 150,
                'stok_actual' => 80, // KRITIS
            ],
            
            // 3. STOK KRITIS - Tepat di batas minimal
            [
                'kode_obat' => 'MED-003',
                'nama_obat' => 'Antasida Tablet',
                'satuan' => 'Tablet',
                'kemasan' => 'Botol',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 1,
                'isi_kemasan_satuan' => 'Botol',
                'isi_per_satuan' => 100,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 100,
                'stok_actual' => 100, // KRITIS - tepat di batas
            ],
            
            // 4. STOK MENIPIS (WARNING) - Sedikit di atas minimal
            [
                'kode_obat' => 'MED-004',
                'nama_obat' => 'Vitamin C 500mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Box',
                'kategori_barang' => 'Vitamin',
                'isi_kemasan_jumlah' => 5,
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 200,
                'stok_actual' => 250, // WARNING - 1.25x minimal
            ],
            
            // 5. STOK MENIPIS (WARNING)
            [
                'kode_obat' => 'MED-005',
                'nama_obat' => 'Omeprazole 20mg',
                'satuan' => 'Kapsul',
                'kemasan' => 'Box',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10,
                'satuan_terkecil' => 'Kapsul',
                'stok_minimal' => 150,
                'stok_actual' => 210, // WARNING - 1.4x minimal
            ],
            
            // 6. STOK MENIPIS (WARNING)
            [
                'kode_obat' => 'MED-006',
                'nama_obat' => 'Cetirizine 10mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Box',
                'kategori_barang' => 'Antihistamin',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 100,
                'stok_actual' => 140, // WARNING - 1.4x minimal
            ],
            
            // 7. STOK AMAN - Di atas warning threshold
            [
                'kode_obat' => 'MED-007',
                'nama_obat' => 'Ibuprofen 400mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Box',
                'kategori_barang' => 'Obat',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 200,
                'stok_actual' => 350, // AMAN - 1.75x minimal
            ],
            
            // 8. STOK AMAN
            [
                'kode_obat' => 'MED-008',
                'nama_obat' => 'Metformin 500mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Box',
                'kategori_barang' => 'Obat Diabetes',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 300,
                'stok_actual' => 600, // AMAN - 2x minimal
            ],
            
            // 9. STOK AMAN
            [
                'kode_obat' => 'MED-009',
                'nama_obat' => 'Captopril 25mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Box',
                'kategori_barang' => 'Antihipertensi',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 250,
                'stok_actual' => 500, // AMAN - 2x minimal
            ],
            
            // 10. STOK AMAN - Sangat banyak
            [
                'kode_obat' => 'MED-010',
                'nama_obat' => 'Alprazolam 0.5mg',
                'satuan' => 'Tablet',
                'kemasan' => 'Box',
                'kategori_barang' => 'Psikotropika',
                'isi_kemasan_jumlah' => 10,
                'isi_kemasan_satuan' => 'Strip',
                'isi_per_satuan' => 10,
                'satuan_terkecil' => 'Tablet',
                'stok_minimal' => 100,
                'stok_actual' => 800, // AMAN - 8x minimal
            ],
        ];

        foreach ($obatList as $obatData) {
            // Pisahkan stok_actual dari data barang medis
            $stokActual = $obatData['stok_actual'];
            unset($obatData['stok_actual']);
            
            // Buat barang medis
            $barang = BarangMedis::create($obatData);
            
            // Buat stok untuk lokasi klinik
            StokBarang::create([
                'id_barang' => $barang->id_obat,
                'id_lokasi' => $lokasi->id,
                'jumlah' => $stokActual, // Stok dalam satuan terkecil
            ]);
            
            // Status stok untuk logging
            $status = 'AMAN';
            if ($stokActual <= $barang->stok_minimal) {
                $status = 'KRITIS';
            } elseif ($stokActual <= $barang->stok_minimal * 1.5) {
                $status = 'WARNING';
            }
            
            $this->command->info("✓ {$barang->nama_obat} - Stok: {$stokActual} {$barang->satuan_terkecil} (Min: {$barang->stok_minimal}) [{$status}]");
        }
        
        $this->command->info('');
        $this->command->info('Seeder selesai!');
        $this->command->info('Ringkasan:');
        $this->command->info('- 3 obat dengan stok KRITIS (≤ minimal)');
        $this->command->info('- 3 obat dengan stok WARNING (> minimal, ≤ 1.5× minimal)');
        $this->command->info('- 4 obat dengan stok AMAN (> 1.5× minimal)');
    }
}
