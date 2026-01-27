<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\User;
use Carbon\Carbon;

class PermintaanBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini membuat:
     * - 5 permintaan barang dengan status PENDING
     * - Beberapa permintaan di bulan ini untuk trending items
     * - Variasi lokasi dan barang yang diminta
     */
    public function run(): void
    {
        // Ambil lokasi klinik
        $lokasi1 = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 1%')->first();
        $lokasi2 = LokasiKlinik::where('nama_lokasi', 'like', '%GKN 2%')->first();
        
        if (!$lokasi1 || !$lokasi2) {
            $this->command->error('Lokasi klinik tidak ditemukan. Jalankan PengadaanSeeder terlebih dahulu.');
            return;
        }

        // Ambil user dokter dari kedua lokasi
        $dokter1 = User::where('akses', 'DOKTER')->where('id_lokasi', $lokasi1->id)->first();
        $dokter2 = User::where('akses', 'DOKTER')->where('id_lokasi', $lokasi2->id)->first();
        
        if (!$dokter1 || !$dokter2) {
            $this->command->error('User dokter tidak ditemukan. Jalankan AdminUserSeeder terlebih dahulu.');
            return;
        }

        // Ambil beberapa barang medis untuk permintaan
        $obatList = BarangMedis::limit(7)->get();
        
        if ($obatList->count() < 5) {
            $this->command->error('Barang medis kurang dari 5. Jalankan BarangMedisSeeder terlebih dahulu.');
            return;
        }

        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        $counter = 1;

        // ========== PERMINTAAN PENDING ==========
        $this->command->info('Membuat permintaan PENDING...');
        
        // 1. Permintaan PENDING dari GKN 1 - Baru hari ini
        $permintaan1 = PermintaanBarang::create([
            'kode_permintaan' => 'REQ-' . Carbon::today()->format('Ymd') . '-001',
            'id_lokasi_peminta' => $lokasi1->id,
            'id_user_peminta' => $dokter1->id,
            'tanggal_permintaan' => Carbon::today(),
            'catatan' => 'Permintaan rutin bulanan untuk stok kritis',
            'status' => 'PENDING',
        ]);

        // Detail: Paracetamol dan Amoxicillin (yang kritis dari seeder sebelumnya)
        DetailPermintaanBarang::create([
            'id_permintaan' => $permintaan1->id,
            'id_barang' => $obatList[0]->id_obat,
            'jumlah_diminta' => 200,
            'kemasan_diminta' => $obatList[0]->kemasan,
            'catatan' => 'Stok kritis, butuh segera',
        ]);

        DetailPermintaanBarang::create([
            'id_permintaan' => $permintaan1->id,
            'id_barang' => $obatList[1]->id_obat,
            'jumlah_diminta' => 150,
            'kemasan_diminta' => $obatList[1]->kemasan,
        ]);

        $this->command->info("✓ {$permintaan1->kode_permintaan} - {$lokasi1->nama_lokasi} [PENDING]");

        // 2. Permintaan PENDING dari GKN 2 - Kemarin
        $permintaan2 = PermintaanBarang::create([
            'kode_permintaan' => 'REQ-' . Carbon::yesterday()->format('Ymd') . '-001',
            'id_lokasi_peminta' => $lokasi2->id,
            'id_user_peminta' => $dokter2->id,
            'tanggal_permintaan' => Carbon::yesterday(),
            'catatan' => 'Permintaan untuk stok menipis',
            'status' => 'PENDING',
        ]);

        DetailPermintaanBarang::create([
            'id_permintaan' => $permintaan2->id,
            'id_barang' => $obatList[3]->id_obat,
            'jumlah_diminta' => 100,
            'kemasan_diminta' => $obatList[3]->kemasan,
        ]);

        DetailPermintaanBarang::create([
            'id_permintaan' => $permintaan2->id,
            'id_barang' => $obatList[4]->id_obat,
            'jumlah_diminta' => 80,
            'kemasan_diminta' => $obatList[4]->kemasan,
        ]);

        $this->command->info("✓ {$permintaan2->kode_permintaan} - {$lokasi2->nama_lokasi} [PENDING]");

        // 3. Permintaan PENDING dengan barang baru (tidak ada di master)
        $permintaan3 = PermintaanBarang::create([
            'kode_permintaan' => 'REQ-' . Carbon::today()->format('Ymd') . '-002',
            'id_lokasi_peminta' => $lokasi1->id,
            'id_user_peminta' => $dokter1->id,
            'tanggal_permintaan' => Carbon::today(),
            'catatan' => 'Permintaan obat baru yang belum terdaftar',
            'status' => 'PENDING',
        ]);

        DetailPermintaanBarang::create([
            'id_permintaan' => $permintaan3->id,
            'id_barang' => null,
            'nama_barang_baru' => 'Salbutamol Inhaler',
            'tipe_barang_baru' => 'OBAT',
            'kemasan_barang_baru' => 'Box',
            'jumlah_diminta' => 20,
            'kemasan_diminta' => 'Box',
            'catatan_barang_baru' => 'Untuk pasien asma',
        ]);

        $this->command->info("✓ {$permintaan3->kode_permintaan} - {$lokasi1->nama_lokasi} [PENDING - Barang Baru]");

        // 4. Permintaan PENDING dari GKN 2 - 3 hari lalu
        $permintaan4 = PermintaanBarang::create([
            'kode_permintaan' => 'REQ-' . Carbon::today()->subDays(3)->format('Ymd') . '-001',
            'id_lokasi_peminta' => $lokasi2->id,
            'id_user_peminta' => $dokter2->id,
            'tanggal_permintaan' => Carbon::today()->subDays(3),
            'catatan' => 'Permintaan untuk persiapan bulan depan',
            'status' => 'PENDING',
        ]);

        DetailPermintaanBarang::create([
            'id_permintaan' => $permintaan4->id,
            'id_barang' => $obatList[5]->id_obat ?? $obatList[2]->id_obat,
            'jumlah_diminta' => 150,
            'kemasan_diminta' => ($obatList[5] ?? $obatList[2])->kemasan,
        ]);

        $this->command->info("✓ {$permintaan4->kode_permintaan} - {$lokasi2->nama_lokasi} [PENDING]");

        // 5. Permintaan PENDING dari GKN 1 - Minggu lalu
        $permintaan5 = PermintaanBarang::create([
            'kode_permintaan' => 'REQ-' . Carbon::today()->subDays(7)->format('Ymd') . '-001',
            'id_lokasi_peminta' => $lokasi1->id,
            'id_user_peminta' => $dokter1->id,
            'tanggal_permintaan' => Carbon::today()->subDays(7),
            'catatan' => 'Permintaan darurat',
            'status' => 'PENDING',
        ]);

        DetailPermintaanBarang::create([
            'id_permintaan' => $permintaan5->id,
            'id_barang' => $obatList[2]->id_obat,
            'jumlah_diminta' => 120,
            'kemasan_diminta' => $obatList[2]->kemasan,
            'catatan' => 'Urgent',
        ]);

        $this->command->info("✓ {$permintaan5->kode_permintaan} - {$lokasi1->nama_lokasi} [PENDING]");

        // ========== PERMINTAAN UNTUK TRENDING (BULAN INI) ==========
        $this->command->info('');
        $this->command->info('Membuat permintaan bulan ini untuk trending items...');

        // Buat beberapa permintaan APPROVED dan COMPLETED bulan ini untuk trending
        $trendingData = [
            // Paracetamol - trending #1
            ['obat_index' => 0, 'jumlah' => 300, 'status' => 'COMPLETED', 'hari_lalu' => 15],
            ['obat_index' => 0, 'jumlah' => 250, 'status' => 'COMPLETED', 'hari_lalu' => 10],
            ['obat_index' => 0, 'jumlah' => 200, 'status' => 'APPROVED', 'hari_lalu' => 5],
            
            // Amoxicillin - trending #2
            ['obat_index' => 1, 'jumlah' => 200, 'status' => 'COMPLETED', 'hari_lalu' => 12],
            ['obat_index' => 1, 'jumlah' => 150, 'status' => 'COMPLETED', 'hari_lalu' => 8],
            
            // Vitamin C - trending #3
            ['obat_index' => 3, 'jumlah' => 180, 'status' => 'COMPLETED', 'hari_lalu' => 14],
            ['obat_index' => 3, 'jumlah' => 120, 'status' => 'APPROVED', 'hari_lalu' => 6],
            
            // Omeprazole - trending #4
            ['obat_index' => 4, 'jumlah' => 100, 'status' => 'COMPLETED', 'hari_lalu' => 18],
            ['obat_index' => 4, 'jumlah' => 90, 'status' => 'COMPLETED', 'hari_lalu' => 4],
        ];

        foreach ($trendingData as $index => $data) {
            $tanggal = Carbon::create($tahunIni, $bulanIni, 1)->addDays($data['hari_lalu']);
            $lokasi = ($index % 2 == 0) ? $lokasi1 : $lokasi2;
            $dokter = ($index % 2 == 0) ? $dokter1 : $dokter2;
            $obat = $obatList[$data['obat_index']];

            $permintaan = PermintaanBarang::create([
                'kode_permintaan' => 'REQ-' . $tanggal->format('Ymd') . '-' . str_pad($index + 10, 3, '0', STR_PAD_LEFT),
                'id_lokasi_peminta' => $lokasi->id,
                'id_user_peminta' => $dokter->id,
                'tanggal_permintaan' => $tanggal,
                'catatan' => 'Permintaan rutin bulan ' . $tanggal->format('F Y'),
                'status' => $data['status'],
            ]);

            DetailPermintaanBarang::create([
                'id_permintaan' => $permintaan->id,
                'id_barang' => $obat->id_obat,
                'jumlah_diminta' => $data['jumlah'],
                'kemasan_diminta' => $obat->kemasan,
                'jumlah_disetujui' => $data['status'] != 'PENDING' ? $data['jumlah'] : null,
            ]);

            $this->command->info("✓ {$permintaan->kode_permintaan} - {$obat->nama_obat} ({$data['jumlah']}) [{$data['status']}]");
        }

        $this->command->info('');
        $this->command->info('Seeder selesai!');
        $this->command->info('Ringkasan:');
        $this->command->info('- 5 permintaan dengan status PENDING (untuk notifikasi)');
        $this->command->info('- 9 permintaan bulan ini (untuk trending items)');
        $this->command->info('- Trending #1: Paracetamol (750 total)');
        $this->command->info('- Trending #2: Amoxicillin (350 total)');
        $this->command->info('- Trending #3: Vitamin C (300 total)');
        $this->command->info('- Trending #4: Omeprazole (190 total)');
    }
}
