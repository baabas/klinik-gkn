<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:create-admin {nip} {password} {--akses=DOKTER}';

    /**
     * The console command description.
     */
    protected $description = 'Membuat user admin (DOKTER/PENGADAAN) baru dari data karyawan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nip = $this->argument('nip');
        $password = $this->argument('password');
        $akses = strtoupper($this->option('akses'));

        if (!in_array($akses, ['DOKTER', 'PENGADAAN'])) {
            $this->error('Akses tidak valid. Gunakan DOKTER atau PENGADAAN.');
            return 1;
        }

        $karyawan = Karyawan::where('nip', $nip)->first();
        if (!$karyawan) {
            $this->error('Gagal. NIP tidak ditemukan di data karyawan.');
            return 1;
        }

        if (User::where('nip', $nip)->exists()) {
            $this->error('Gagal. Akun untuk NIP ini sudah terdaftar.');
            return 1;
        }

        // Buat user baru
        $user = User::create([
            'nama_karyawan' => $karyawan->nama_karyawan,
            'nip' => $karyawan->nip,
            'email' => $karyawan->email,
            'password' => Hash::make($password),
        ]);

        // Berikan peran yang sesuai
        $adminRole = Role::where('name', $akses)->first();
        if ($adminRole) {
            $user->roles()->attach($adminRole);
        }

        $this->info("Akun untuk NIP {$nip} dengan akses {$akses} berhasil dibuat!");
        return 0;
    }
}
