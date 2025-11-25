<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikasi
        DB::table('karyawan')->delete();

        DB::table('karyawan')->insert([
            ['nip' => '198507202009444444', 'nama_karyawan' => 'Fajar Nugroho', 'jabatan' => 'Kepala Seksi', 'kantor' => 'KPP Madya SMG', 'email' => 'fajar.nugroho@example.com', 'tanggal_lahir' => '1985-07-20'],
            ['nip' => '198702142010333333', 'nama_karyawan' => 'Oscar Mahendra', 'jabatan' => 'Kepala Seksi', 'kantor' => 'KPP Madya SMG', 'email' => 'oscar.m@example.com', 'tanggal_lahir' => '1987-02-14'],
            ['nip' => '198811222010555555', 'nama_karyawan' => 'Doni Firmansyah', 'jabatan' => 'Fungsional Pemeriksa', 'kantor' => 'KPP Gayam Sari', 'email' => 'doni.f@example.com', 'tanggal_lahir' => '1988-11-22'],
            ['nip' => '333333333333333333', 'nama_karyawan' => 'admin3', 'jabatan' => 'Staff Pengadaan', 'kantor' => 'KPP Pratama SMG', 'email' => 'admin3@example.com', 'tanggal_lahir' => '1990-01-01'],

        ]);
    }
}
