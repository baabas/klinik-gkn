<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Karyawan;

class SyncKaryawanNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'karyawan:sync-names {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync karyawan names from users table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        // Step 1: Update existing karyawan with empty names
        $this->info('📝 Step 1: Updating karyawan with empty names...');
        
        $toUpdate = DB::table('karyawan as k')
            ->join('users as u', 'k.nip', '=', 'u.nip')
            ->whereRaw('(k.nama_karyawan IS NULL OR k.nama_karyawan = "" OR TRIM(k.nama_karyawan) = "")')
            ->whereNotNull('u.nama_karyawan')
            ->whereRaw('TRIM(u.nama_karyawan) != ""')
            ->select('k.nip', 'k.nama_karyawan as old_name', 'u.nama_karyawan as new_name')
            ->get();

        if ($toUpdate->isEmpty()) {
            $this->info('   ✅ No empty names found');
        } else {
            $this->table(
                ['NIP', 'Old Name', 'New Name'],
                $toUpdate->map(fn($row) => [
                    $row->nip,
                    $row->old_name ?: '(empty)',
                    $row->new_name
                ])
            );

            if (!$dryRun) {
                $affected = DB::table('karyawan as k')
                    ->join('users as u', 'k.nip', '=', 'u.nip')
                    ->whereRaw('(k.nama_karyawan IS NULL OR k.nama_karyawan = "" OR TRIM(k.nama_karyawan) = "")')
                    ->whereNotNull('u.nama_karyawan')
                    ->whereRaw('TRIM(u.nama_karyawan) != ""')
                    ->update(['k.nama_karyawan' => DB::raw('u.nama_karyawan')]);

                $this->info("   ✅ Updated {$affected} records");
            }
        }

        $this->newLine();

        // Step 2: Insert missing users as new karyawan
        $this->info('📝 Step 2: Inserting missing users into karyawan...');
        
        $toInsert = DB::table('users as u')
            ->leftJoin('karyawan as k', 'u.nip', '=', 'k.nip')
            ->whereNotNull('u.nip')
            ->whereNull('k.nip')
            ->select('u.nip', 'u.nama_karyawan')
            ->get();

        if ($toInsert->isEmpty()) {
            $this->info('   ✅ All users already in karyawan table');
        } else {
            $this->table(
                ['NIP', 'Name'],
                $toInsert->map(fn($row) => [
                    $row->nip,
                    $row->nama_karyawan
                ])
            );

            if (!$dryRun) {
                foreach ($toInsert as $user) {
                    Karyawan::create([
                        'nip' => $user->nip,
                        'nama_karyawan' => $user->nama_karyawan,
                        'no_hp' => '-',
                        'id_lokasi' => 1,
                    ]);
                }
                $this->info("   ✅ Inserted {$toInsert->count()} records");
            }
        }

        $this->newLine();

        // Step 3: Verification
        $this->info('🔍 Verification:');
        
        $stats = DB::table('users as u')
            ->leftJoin('karyawan as k', 'u.nip', '=', 'k.nip')
            ->whereNotNull('u.nip')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN k.nama_karyawan IS NOT NULL AND k.nama_karyawan != "" THEN 1 ELSE 0 END) as filled,
                SUM(CASE WHEN k.nama_karyawan IS NULL OR k.nama_karyawan = "" THEN 1 ELSE 0 END) as empty
            ')
            ->first();

        $this->table(
            ['Total Users', 'With Name', 'Still Empty'],
            [[$stats->total, $stats->filled, $stats->empty]]
        );

        if ($stats->empty > 0) {
            $this->warn("⚠️  {$stats->empty} records still have empty names");
            
            $emptyRecords = DB::table('users as u')
                ->leftJoin('karyawan as k', 'u.nip', '=', 'k.nip')
                ->whereNotNull('u.nip')
                ->whereRaw('(k.nama_karyawan IS NULL OR k.nama_karyawan = "")')
                ->select('u.id', 'u.nip', 'u.nama_karyawan as user_name', 'k.nama_karyawan as karyawan_name')
                ->limit(5)
                ->get();

            $this->newLine();
            $this->info('First 5 records with empty names:');
            $this->table(
                ['User ID', 'NIP', 'User Name', 'Karyawan Name'],
                $emptyRecords->map(fn($r) => [
                    $r->id,
                    $r->nip,
                    $r->user_name,
                    $r->karyawan_name ?: '(empty)'
                ])
            );
        }

        $this->newLine();

        if ($dryRun) {
            $this->info('🔍 DRY RUN completed - No changes were made');
            $this->info('💡 Run without --dry-run to apply changes');
        } else {
            $this->info('✅ Sync completed successfully!');
        }

        return 0;
    }
}
