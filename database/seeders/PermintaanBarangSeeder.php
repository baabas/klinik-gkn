<?php

namespace Database\Seeders;

use App\Models\BarangMedis;
use App\Models\PermintaanBarang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\LokasiKlinik;

class PermintaanBarangSeeder extends Seeder
{
    public function run(): void
    {
        $dokter = User::whereHas('roles', fn ($query) => $query->where('name', 'DOKTER'))->first();
        $lokasi = null;

        if ($dokter) {
            $lokasi = $dokter->id_lokasi ? LokasiKlinik::find($dokter->id_lokasi) : LokasiKlinik::first();
        }

        if (! $dokter || ! $lokasi) {
            Log::warning('PermintaanBarangSeeder: Dokter atau lokasi tidak ditemukan. Lewati seeding permintaan.');
            return;
        }

        $barangTerpilih = BarangMedis::with('kemasanBarang')->limit(2)->get();

        if ($barangTerpilih->count() < 2) {
            Log::warning('PermintaanBarangSeeder: Data barang medis belum mencukupi.');
            return;
        }

        DB::transaction(function () use ($dokter, $lokasi, $barangTerpilih) {
            $permintaan = PermintaanBarang::create([
                'kode' => PermintaanBarang::generateKode(),
                'tanggal' => now()->toDateString(),
                'peminta_id' => $dokter->id,
                'lokasi_id' => $lokasi->id,
                'status' => PermintaanBarang::STATUS_DRAFT,
                'catatan' => 'Contoh permintaan awal dari seeder.',
            ]);

            foreach ($barangTerpilih as $barang) {
                $kemasan = $barang->kemasanBarang->first();

                if (! $kemasan) {
                    continue;
                }

                $permintaan->details()->create([
                    'barang_id' => $barang->id_obat,
                    'barang_kemasan_id' => $kemasan->id,
                    'kemasan_id' => $kemasan->id,
                    'jumlah' => 2,
                    'jumlah_kemasan' => 2,
                    'isi_per_kemasan' => $kemasan->isi_per_kemasan,
                    'total_unit' => $kemasan->isi_per_kemasan * 2,
                    'total_unit_dasar' => $kemasan->isi_per_kemasan * 2,
                    'satuan' => $barang->satuan_dasar,
                    'base_unit' => $barang->satuan_dasar,
                    'kemasan' => $kemasan->nama_kemasan,
                    'satuan_kemasan' => $kemasan->nama_kemasan,
                    'keterangan' => 'Permintaan rutin',
                ]);
            }

            $permintaan->details()->create([
                'nama_barang_baru' => 'Vitamin D 1000IU',
                'jumlah' => 5,
                'satuan' => 'Tablet',
                'kemasan' => 'Botol isi 30',
                'keterangan' => 'Permintaan barang baru dari dokter',
            ]);
        });
    }
}
