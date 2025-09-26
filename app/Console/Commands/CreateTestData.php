<?php

namespace App\Console\Commands;

use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use App\Models\BarangMedis;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateTestData extends Command
{
    protected $signature = 'test:create-data';
    protected $description = 'Create test data for dashboard';

    public function handle()
    {
        $this->info('Creating test data...');

        // Get some users and barang
        $users = User::with('roles')->take(3)->get();
        $barangMedis = BarangMedis::take(5)->get();

        if ($users->isEmpty() || $barangMedis->isEmpty()) {
            $this->error('Need users and barang_medis data first!');
            return;
        }

        // Create some permintaan with different statuses
        $statuses = ['PENDING', 'APPROVED', 'COMPLETED', 'REJECTED'];
        
        foreach ($statuses as $status) {
            for ($i = 0; $i < 2; $i++) {
                $user = $users->random();
                
                $permintaan = PermintaanBarang::create([
                    'kode_permintaan' => 'TEST-' . date('Ymd') . '-' . strtoupper(Str::random(4)),
                    'tanggal_permintaan' => now()->subDays(rand(1, 30)),
                    'catatan' => 'Test permintaan ' . $status,
                    'status' => $status,
                    'id_user_peminta' => $user->id,
                    'id_lokasi_peminta' => $user->id_lokasi ?? 1,
                ]);

                // Add some detail
                foreach ($barangMedis->take(rand(1, 3)) as $barang) {
                    DetailPermintaanBarang::create([
                        'id_permintaan' => $permintaan->id,
                        'id_barang' => $barang->id_obat,
                        'jumlah_diminta' => rand(1, 10),
                        'jumlah_disetujui' => $status === 'APPROVED' ? rand(1, 10) : null,
                        'kemasan_diminta' => 'Box',
                        'catatan' => null,
                    ]);
                }

                // Also create some barang baru requests
                DetailPermintaanBarang::create([
                    'id_permintaan' => $permintaan->id,
                    'id_barang' => null,
                    'jumlah_diminta' => rand(1, 5),
                    'nama_barang_baru' => 'Test Barang Baru ' . $i,
                    'tipe_barang_baru' => null,
                    'kemasan_barang_baru' => 'Box',
                    'catatan_barang_baru' => null,
                ]);
            }
        }

        $this->info('Test data created successfully!');
        return 0;
    }
}
