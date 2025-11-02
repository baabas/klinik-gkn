<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\RekamMedis;

class FixTanggalKunjungan extends Command
{
    protected $signature = 'rekam-medis:fix-waktu';
    protected $description = 'Fix tanggal_kunjungan yang waktu-nya 00:00:00';

    public function handle()
    {
        $this->info('🔍 Mencari rekam medis dengan waktu 00:00:00...');
        
        // Cek berapa banyak yang perlu diupdate
        $count = DB::table('rekam_medis')
            ->whereRaw('TIME(tanggal_kunjungan) = "00:00:00"')
            ->count();
        
        if ($count === 0) {
            $this->info('✅ Semua data sudah memiliki waktu yang benar!');
            return 0;
        }
        
        $this->info("📝 Ditemukan {$count} rekam medis dengan waktu 00:00:00");
        $this->newLine();
        
        // Update dengan created_at jika ada
        $this->info('Step 1: Update dengan waktu dari created_at...');
        $updated1 = DB::table('rekam_medis')
            ->whereRaw('TIME(tanggal_kunjungan) = "00:00:00"')
            ->whereNotNull('created_at')
            ->update(['tanggal_kunjungan' => DB::raw('created_at')]);
        
        $this->info("   ✅ Updated {$updated1} records dengan created_at");
        
        // Update sisanya dengan waktu sekarang
        $this->info('Step 2: Update sisanya dengan waktu sekarang...');
        $updated2 = DB::table('rekam_medis')
            ->whereRaw('TIME(tanggal_kunjungan) = "00:00:00"')
            ->whereNull('created_at')
            ->update([
                'tanggal_kunjungan' => DB::raw('CONCAT(DATE(tanggal_kunjungan), " ", CURTIME())')
            ]);
        
        $this->info("   ✅ Updated {$updated2} records dengan waktu sekarang");
        
        $this->newLine();
        
        // Verifikasi
        $remaining = DB::table('rekam_medis')
            ->whereRaw('TIME(tanggal_kunjungan) = "00:00:00"')
            ->count();
        
        if ($remaining === 0) {
            $this->info('✅ Semua data berhasil diupdate!');
            $this->info("📊 Total diupdate: " . ($updated1 + $updated2) . " records");
        } else {
            $this->warn("⚠️  Masih ada {$remaining} records dengan waktu 00:00:00");
        }
        
        return 0;
    }
}
