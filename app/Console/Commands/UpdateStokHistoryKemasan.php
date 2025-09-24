<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StokHistory;
use App\Models\BarangMedis;

class UpdateStokHistoryKemasan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stok:update-kemasan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update StokHistory records to populate missing jumlah_kemasan data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update StokHistory records...');
        
        // Ambil semua StokHistory yang jumlah_kemasan-nya null atau 0
        $histories = StokHistory::whereNull('jumlah_kemasan')
            ->orWhere('jumlah_kemasan', 0)
            ->where('perubahan', '>', 0) // Hanya untuk transaksi masuk
            ->with('barang')
            ->get();
            
        $updated = 0;
        
        foreach ($histories as $history) {
            if (!$history->barang) continue;
            
            $barang = $history->barang;
            
            // Hitung kemasan berdasarkan perubahan dan data master barang
            $isiPerKemasan = ($barang->isi_kemasan_jumlah ?? 1) * ($barang->isi_per_satuan ?? 1);
            $jumlahKemasan = $isiPerKemasan > 0 ? ceil($history->perubahan / $isiPerKemasan) : 1;
            
            // Update record
            $history->update([
                'jumlah_kemasan' => $jumlahKemasan,
                'isi_per_kemasan' => $isiPerKemasan,
                'satuan_kemasan' => $barang->kemasan ?? 'Box'
            ]);
            
            $updated++;
        }
        
        $this->info("Successfully updated {$updated} StokHistory records.");
        return Command::SUCCESS;
    }
}
