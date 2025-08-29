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
            ['nip' => '198507202009021004', 'nama_karyawan' => 'Fajar Nugroho', 'jabatan' => 'Kepala Seksi', 'kantor' => 'KPP Madya SMG', 'email' => 'fajar.nugroho@example.com', 'alamat' => 'Jl. Simpang Lima No. 1, Semarang', 'agama' => 'Hindu', 'tanggal_lahir' => '1985-07-20'],
            ['nip' => '198702142010121011', 'nama_karyawan' => 'Oscar Mahendra', 'jabatan' => 'Kepala Seksi', 'kantor' => 'KPP Madya SMG', 'email' => 'oscar.m@example.com', 'alamat' => 'Jl. Papandayan No. 33, Semarang', 'agama' => 'Buddha', 'tanggal_lahir' => '1987-02-14'],
            ['nip' => '198811222010121005', 'nama_karyawan' => 'Doni Firmansyah', 'jabatan' => 'Fungsional Pemeriksa', 'kantor' => 'KPP Gayam Sari', 'email' => 'doni.f@example.com', 'alamat' => 'Jl. Gajah Mada No. 55, Semarang', 'agama' => 'Katolik', 'tanggal_lahir' => '1988-11-22'],
            ['nip' => '198904122014121008', 'nama_karyawan' => 'Joko Susilo', 'jabatan' => 'Fungsional Pemeriksa', 'kantor' => 'KPP Madya SMG', 'email' => 'joko.s@example.com', 'alamat' => 'Jl. Diponegoro No. 76, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1989-04-12'],
            ['nip' => '199001152015031001', 'nama_karyawan' => 'Budi Santoso', 'jabatan' => 'Analis Kebijakan', 'kantor' => 'Kanwil', 'email' => 'budi.santoso@example.com', 'alamat' => 'Jl. Pahlawan No. 10, Semarang', 'agama' => 'Kristen', 'tanggal_lahir' => '1990-01-15'],
            ['nip' => '199008172015031009', 'nama_karyawan' => 'Nanda Kusuma', 'jabatan' => 'Analis Kebijakan', 'kantor' => 'Kanwil', 'email' => 'nanda.k@example.com', 'alamat' => 'Jl. Sisingamangaraja No. 11, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1990-08-17'],
            ['nip' => '199107072016021002', 'nama_karyawan' => 'Tegar Setiawan', 'jabatan' => 'Account Representative', 'kantor' => 'KPP SMG Tengah 1', 'email' => 'tegar.s@example.com', 'alamat' => 'Jl. MT Haryono No. 555, Semarang', 'agama' => 'Kristen', 'tanggal_lahir' => '1991-07-07'],
            ['nip' => '199109052014102001', 'nama_karyawan' => 'Gita Wulandari', 'jabatan' => 'Pelaksana', 'kantor' => 'KPP SMG Selatan', 'email' => 'gita.w@example.com', 'alamat' => 'Jl. Setiabudi No. 200, Semarang', 'agama' => 'Buddha', 'tanggal_lahir' => '1991-09-05'],
            ['nip' => '199205202016072002', 'nama_karyawan' => 'Ani Lestari', 'jabatan' => 'Staf Keuangan', 'kantor' => 'PT Gumilang', 'email' => 'ani.lestari@example.com', 'alamat' => 'Jl. Gatot Subroto No. 5, Jakarta', 'agama' => 'Islam', 'tanggal_lahir' => '1992-05-20'],
            ['nip' => '199212012017011003', 'nama_karyawan' => 'Leo Pratama', 'jabatan' => 'Account Representative', 'kantor' => 'KPP SMG Tengah 1', 'email' => 'leo.p@example.com', 'alamat' => 'Jl. Sriwijaya No. 45, Semarang', 'agama' => 'Katolik', 'tanggal_lahir' => '1992-12-01'],
            ['nip' => '199303122017052002', 'nama_karyawan' => 'Eka Putri', 'jabatan' => 'Pranata Komputer', 'kantor' => 'KPTIK', 'email' => 'eka.putri@example.com', 'alamat' => 'Jl. Pandanaran No. 88, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1993-03-12'],
            ['nip' => '199309112018011006', 'nama_karyawan' => 'Rian Hidayat', 'jabatan' => 'Pranata Komputer', 'kantor' => 'KPTIK', 'email' => 'rian.h@example.com', 'alamat' => 'Jl. Imam Bonjol No. 170, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1993-09-11'],
            ['nip' => '199406302019032002', 'nama_karyawan' => 'Kartika Sari', 'jabatan' => 'Staf Keuangan', 'kantor' => 'PT Gumilang', 'email' => 'kartika.sari@example.com', 'alamat' => 'Jl. Teuku Umar No. 19, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1994-06-30'],
            ['nip' => '199508102018012003', 'nama_karyawan' => 'Citra Dewi', 'jabatan' => 'Account Representative', 'kantor' => 'KPP SMG Tengah 1', 'email' => 'citra.dewi@example.com', 'alamat' => 'Jl. Pemuda No. 121, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1995-08-10'],
            ['nip' => '199511032019032007', 'nama_karyawan' => 'Siska Novita', 'jabatan' => 'Staf Keuangan', 'kantor' => 'PT Gumilang', 'email' => 'siska.n@example.com', 'alamat' => 'Jl. Medoho Raya No. 12, Semarang', 'agama' => 'Konghucu', 'tanggal_lahir' => '1995-11-03'],
            ['nip' => '199602182020011001', 'nama_karyawan' => 'Hendra Wijaya', 'jabatan' => 'Staf IT', 'kantor' => 'KPTIK', 'email' => 'hendra.w@example.com', 'alamat' => 'Jl. Majapahit No. 301, Semarang', 'agama' => 'Kristen', 'tanggal_lahir' => '1996-02-18'],
            ['nip' => '199603282020012009', 'nama_karyawan' => 'Utami Dewi', 'jabatan' => 'Pelaksana', 'kantor' => 'KPP Gayam Sari', 'email' => 'utami.d@example.com', 'alamat' => 'Jl. Ki Mangunsarkoro No. 23, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1996-03-28'],
            ['nip' => '199705252020022005', 'nama_karyawan' => 'Maria Anggraini', 'jabatan' => 'Pelaksana', 'kantor' => 'KPP Gayam Sari', 'email' => 'maria.a@example.com', 'alamat' => 'Jl. Candi Baru No. 8, Semarang', 'agama' => 'Kristen', 'tanggal_lahir' => '1997-05-25'],
            ['nip' => '199810052021032004', 'nama_karyawan' => 'Indah Permata', 'jabatan' => 'Analis SDM', 'kantor' => 'Kanwil', 'email' => 'indah.permata@example.com', 'alamat' => 'Jl. Veteran No. 22, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1998-10-05'],
            ['nip' => '199901202022032001', 'nama_karyawan' => 'Putri Amelia', 'jabatan' => 'Staf Administrasi', 'kantor' => 'KPP SMG Selatan', 'email' => 'putri.a@example.com', 'alamat' => 'Jl. Sultan Agung No. 150, Semarang', 'agama' => 'Islam', 'tanggal_lahir' => '1999-01-20'],
        ]);
    }
}
