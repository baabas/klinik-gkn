<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaftarPenyakitSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('daftar_penyakit')->delete();
        DB::table('daftar_penyakit')->insert([
            ['kode_penyakit' => 'ALR', 'nama_penyakit' => 'Alergi'],
            ['kode_penyakit' => 'ANM', 'nama_penyakit' => 'Anemia'],
            ['kode_penyakit' => 'AUR', 'nama_penyakit' => 'Asam Urat'],
            ['kode_penyakit' => 'ASM', 'nama_penyakit' => 'Asma'],
            ['kode_penyakit' => 'BGN', 'nama_penyakit' => 'Batu Ginjal'],
            ['kode_penyakit' => 'BRK', 'nama_penyakit' => 'Bronkitis'],
            ['kode_penyakit' => 'CAR', 'nama_penyakit' => 'Cacar Air'],
            ['kode_penyakit' => 'CPK', 'nama_penyakit' => 'Campak'],
            ['kode_penyakit' => 'DMM', 'nama_penyakit' => 'Demam'],
            ['kode_penyakit' => 'DBD', 'nama_penyakit' => 'Demam Berdarah Dengue'],
            ['kode_penyakit' => 'DMT', 'nama_penyakit' => 'Dermatitis'],
            ['kode_penyakit' => 'DM', 'nama_penyakit' => 'Diabetes Melitus'],
            ['kode_penyakit' => 'DRE', 'nama_penyakit' => 'Diare'],
            ['kode_penyakit' => 'GTS', 'nama_penyakit' => 'Gastritis (Maag)'],
            ['kode_penyakit' => 'HPS', 'nama_penyakit' => 'Hepatitis'],
            ['kode_penyakit' => 'HPT', 'nama_penyakit' => 'Hipertensi'],
            ['kode_penyakit' => 'ISK', 'nama_penyakit' => 'Infeksi Saluran Kemih'],
            ['kode_penyakit' => 'FLU', 'nama_penyakit' => 'Influenza'],
            ['kode_penyakit' => 'INS', 'nama_penyakit' => 'Insomnia'],
            ['kode_penyakit' => 'KJT', 'nama_penyakit' => 'Konjungtivitis (Sakit Mata)'],
            ['kode_penyakit' => 'LKM', 'nama_penyakit' => 'Leukemia'],
            ['kode_penyakit' => 'MGR', 'nama_penyakit' => 'Migrain'],
            ['kode_penyakit' => 'PNM', 'nama_penyakit' => 'Pneumonia'],
            ['kode_penyakit' => 'RUB', 'nama_penyakit' => 'Radang Usus Buntu'],
            ['kode_penyakit' => 'SJG', 'nama_penyakit' => 'Serangan Jantung'],
            ['kode_penyakit' => 'SNS', 'nama_penyakit' => 'Sinusitis'],
            ['kode_penyakit' => 'STR', 'nama_penyakit' => 'Stroke'],
            ['kode_penyakit' => 'TFS', 'nama_penyakit' => 'Tifus'],
            ['kode_penyakit' => 'TBC', 'nama_penyakit' => 'Tuberkulosis'],
            ['kode_penyakit' => 'VTG', 'nama_penyakit' => 'Vertigo']

        ]);
    }
}
