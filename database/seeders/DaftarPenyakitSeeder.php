<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DaftarPenyakitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/data/gabungan_icd10.csv');

        if (!File::exists($csvFile)) {
            $this->command->error("File CSV tidak ditemukan di: " . $csvFile);
            return;
        }

        Schema::disableForeignKeyConstraints();
        DB::table('daftar_penyakit')->truncate();
        Schema::enableForeignKeyConstraints();

        // [PERBAIKAN] Membaca file dan mengonversi encoding ke UTF-8
        $fileContent = File::get($csvFile);
        $utf8Content = mb_convert_encoding($fileContent, 'UTF-8', 'auto');
        $rows = array_map('str_getcsv', explode("\n", $utf8Content));
        
        // Menghapus baris header (baris pertama)
        array_shift($rows);

        foreach ($rows as $data) {
            // Lewati baris kosong
            if (empty($data[0])) {
                continue;
            }

            if (isset($data[0]) && isset($data[1])) {
                DB::table('daftar_penyakit')->insert([
                    'ICD10' => $data[0],
                    'nama_penyakit' => $data[1],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('Seeding data ICD-10 dari file CSV berhasil.');
    }
}