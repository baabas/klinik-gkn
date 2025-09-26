<?php

namespace App\Console\Commands;

use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use App\Models\User;
use Illuminate\Console\Command;

class CleanTestData extends Command
{
    protected $signature = 'test:clean-data';
    protected $description = 'Clean test data from database';

    public function handle()
    {
        $this->info('Cleaning test data...');

        // Delete test permintaan and their details
        $testPermintaan = PermintaanBarang::where('kode_permintaan', 'LIKE', 'TEST-%')->get();
        
        $deletedCount = 0;
        foreach ($testPermintaan as $permintaan) {
            // Delete details first
            DetailPermintaanBarang::where('id_permintaan', $permintaan->id)->delete();
            // Delete permintaan
            $permintaan->delete();
            $deletedCount++;
        }

        $this->info("Deleted {$deletedCount} test permintaan records with their details.");

        // Delete test user
        $testUser = User::where('email', 'pengadaan@klinik.test')->first();
        if ($testUser) {
            // Detach roles first
            $testUser->roles()->detach();
            // Delete user
            $testUser->delete();
            $this->info('Deleted test PENGADAAN user.');
        }

        $this->info('Test data cleaned successfully!');
        return 0;
    }
}
