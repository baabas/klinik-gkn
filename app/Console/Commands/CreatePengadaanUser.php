<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreatePengadaanUser extends Command
{
    protected $signature = 'user:create-pengadaan';
    protected $description = 'Create a PENGADAAN user for testing';

    public function handle()
    {
        // Check if PENGADAAN role exists
        $pengadaanRole = Role::where('name', 'PENGADAAN')->first();
        if (!$pengadaanRole) {
            $this->error('PENGADAAN role not found!');
            return;
        }

        // Create or find user
        $user = User::firstOrCreate(
            ['email' => 'pengadaan@klinik.test'],
            [
                'nama_karyawan' => 'Staff Pengadaan',
                'nip' => 'PG001',
                'nik' => null,
                'password' => Hash::make('password'),
                'akses' => 'PENGADAAN'
            ]
        );

        // Attach role if not already attached
        if (!$user->hasRole('PENGADAAN')) {
            $user->roles()->attach($pengadaanRole->id);
            $this->info('PENGADAAN role attached to user.');
        }

        $this->info('PENGADAAN user created/found:');
        $this->info('Email: ' . $user->email);
        $this->info('Password: password');
        
        return 0;
    }
}
