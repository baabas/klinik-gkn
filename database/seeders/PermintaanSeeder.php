<?php

namespace Database\Seeders;

use App\Models\BarangMedis;
use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use App\Models\StokBarang;
use App\Models\User;
use App\Models\LokasiKlinik;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PermintaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset data permintaan agar hasil seeder konsisten
        $this->resetDataPermintaan();

        // 2. Buat permintaan dengan berbagai status
        $this->buatPermintaanPending();
        $this->buatPermintaanApproved();
        $this->buatPermintaanCompleted();

        $this->command->info('Data Permintaan dan Stok Kritis berhasil dibuat');
    }

    /**
     * Reset data permintaan dan stok agar seeder konsisten.
     */
    private function resetDataPermintaan(): void
    {
        DetailPermintaanBarang::query()->delete();
        PermintaanBarang::query()->delete();
        
        // Pastikan stok tiap barang tetap aman, sehingga dashboard stok kritis bisa bernilai 0.
        foreach (BarangMedis::all() as $barang) {
            $stokTotal = StokBarang::where('id_barang', $barang->id_obat)->sum('jumlah');

            if ($stokTotal < ($barang->stok_minimal * 4)) {
                StokBarang::where('id_barang', $barang->id_obat)->increment('jumlah', ($barang->stok_minimal * 4) - $stokTotal);
            }
        }

        $this->command->info('✓ Data permintaan lama dihapus dan stok disesuaikan agar aman');
    }

    /**
     * Buat permintaan dengan status PENDING
     */
    private function buatPermintaanPending(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $lokasi = LokasiKlinik::first();

        if (!$user || !$lokasi) {
            $this->command->warn('User atau Lokasi tidak ditemukan untuk PENDING');
            return;
        }

        $dataPending = [
            [
                'kode' => 'PRM' . date('YmdHis') . '001',
                'tanggal' => Carbon::now()->subDays(2)->toDateString(),
                'catatan' => 'Permintaan obat untuk persediaan rutin',
                'details' => [
                    ['id_barang' => 1, 'jumlah_diminta' => 50, 'kemasan_diminta' => 'Strip @10'],
                ],
            ],
            [
                'kode' => 'PRM' . date('YmdHis') . '002',
                'tanggal' => Carbon::now()->subDays(1)->toDateString(),
                'catatan' => 'Permintaan alat dan APD tambahan',
                'details' => [
                    ['id_barang' => 10, 'jumlah_diminta' => 20, 'kemasan_diminta' => 'Box'],
                ],
            ],
        ];

        foreach ($dataPending as $item) {
            $permintaan = PermintaanBarang::create([
                'kode_permintaan' => $item['kode'],
                'id_lokasi_peminta' => $lokasi->id,
                'id_user_peminta' => $user->id,
                'tanggal_permintaan' => $item['tanggal'],
                'catatan' => $item['catatan'],
                'status' => 'PENDING',
            ]);

            foreach ($item['details'] as $detail) {
                DetailPermintaanBarang::create(array_merge([
                    'id_permintaan' => $permintaan->id,
                ], $detail));
            }
        }

        $this->command->info('✓ Permintaan PENDING berhasil dibuat (2 permintaan)');
    }

    /**
     * Buat permintaan dengan status APPROVED
     */
    private function buatPermintaanApproved(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $lokasi = LokasiKlinik::first();

        if (!$user || !$lokasi) {
            $this->command->warn('User atau Lokasi tidak ditemukan untuk APPROVED');
            return;
        }

        for ($i = 1; $i <= 4; $i++) {
            $permintaan = PermintaanBarang::create([
            'kode_permintaan' => 'APR' . date('YmdHis') . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'id_lokasi_peminta' => $lokasi->id,
                'id_user_peminta' => $user->id,
                'tanggal_permintaan' => Carbon::now()->subDays(5 + $i)->toDateString(),
                'catatan' => 'Permintaan yang sudah disetujui #' . $i,
                'status' => 'APPROVED',
            ]);

            DetailPermintaanBarang::create([
                'id_permintaan' => $permintaan->id,
                'id_barang' => ($i % 5) + 1,
                'jumlah_diminta' => 20 + ($i * 5),
                'jumlah_disetujui' => 20 + ($i * 5),
                'kemasan_diminta' => 'Strip @10',
            ]);
        }

        $this->command->info('✓ Permintaan APPROVED berhasil dibuat (4 permintaan)');
    }

    /**
     * Buat permintaan dengan status COMPLETED
     */
    private function buatPermintaanCompleted(): void
    {
        $user = User::where('email', 'admin@example.com')->first();
        $lokasi = LokasiKlinik::first();

        if (!$user || !$lokasi) {
            $this->command->warn('User atau Lokasi tidak ditemukan untuk COMPLETED');
            return;
        }

        for ($i = 1; $i <= 4; $i++) {
            $permintaan = PermintaanBarang::create([
                'kode_permintaan' => 'CMP' . date('YmdHis') . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'id_lokasi_peminta' => $lokasi->id,
                'id_user_peminta' => $user->id,
                'tanggal_permintaan' => Carbon::now()->subDays(10 + $i)->toDateString(),
                'catatan' => 'Permintaan yang sudah selesai #' . $i,
                'status' => 'COMPLETED',
            ]);

            DetailPermintaanBarang::create([
                'id_permintaan' => $permintaan->id,
                'id_barang' => ($i % 5) + 1,
                'jumlah_diminta' => 15 + ($i * 3),
                'jumlah_disetujui' => 15 + ($i * 3),
                'kemasan_diminta' => 'Strip @10',
            ]);
        }

        $this->command->info('✓ Permintaan COMPLETED berhasil dibuat (4 permintaan)');
    }
}
