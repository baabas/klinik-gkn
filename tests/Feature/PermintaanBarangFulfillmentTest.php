<?php

namespace Tests\Feature;

use App\Models\BarangKemasan;
use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\PermintaanBarang;
use App\Models\PermintaanBarangDetail;
use App\Models\Role;
use App\Models\StokBarang;
use App\Models\StokHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PermintaanBarangFulfillmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_fulfill_increments_location_stock_and_logs_history(): void
    {
        $lokasiKlinik = LokasiKlinik::create([
            'nama_lokasi' => 'Klinik A',
            'alamat' => 'Jalan Klinik',
        ]);

        $barang = BarangMedis::create([
            'kode_obat' => 'OBT-0001',
            'nama_obat' => 'Paracetamol',
            'tipe' => 'OBAT',
            'satuan_dasar' => 'tablet',
            'stok' => 100,
        ]);

        $kemasan = BarangKemasan::create([
            'barang_id' => $barang->id_obat,
            'nama_kemasan' => 'Strip',
            'isi_per_kemasan' => 4,
            'is_default' => true,
        ]);

        StokBarang::create([
            'id_barang' => $barang->id_obat,
            'id_lokasi' => $lokasiKlinik->id,
            'jumlah' => 100,
        ]);

        $peminta = User::create([
            'nama_karyawan' => 'Dokter Perminta',
            'nip' => '1987123456789012',
            'email' => 'dokter@example.com',
            'password' => Hash::make('password'),
            'akses' => 'DOKTER',
            'id_lokasi' => $lokasiKlinik->id,
        ]);

        $permintaan = PermintaanBarang::create([
            'kode' => 'REQ-TEST-0001',
            'tanggal' => now()->toDateString(),
            'peminta_id' => $peminta->id,
            'lokasi_id' => $lokasiKlinik->id,
            'status' => PermintaanBarang::STATUS_DISETUJUI,
        ]);

        PermintaanBarangDetail::create([
            'permintaan_id' => $permintaan->id,
            'barang_id' => $barang->id_obat,
            'barang_kemasan_id' => $kemasan->id,
            'kemasan_id' => $kemasan->id,
            'jumlah' => 3,
            'jumlah_kemasan' => 3,
            'isi_per_kemasan' => 4,
            'total_unit' => 12,
            'total_unit_dasar' => 12,
            'satuan' => 'Pack',
            'base_unit' => 'tablet',
            'kemasan' => 'Strip',
            'satuan_kemasan' => 'Strip',
        ]);

        $rolePengadaan = Role::create(['name' => 'PENGADAAN']);

        $petugasPengadaan = User::create([
            'nama_karyawan' => 'Petugas Pengadaan',
            'nip' => '1977123456789012',
            'email' => 'pengadaan@example.com',
            'password' => Hash::make('password'),
            'akses' => 'PENGADAAN',
        ]);

        $petugasPengadaan->roles()->attach($rolePengadaan->id);

        $response = $this->actingAs($petugasPengadaan)->post(route('permintaan.fulfill', $permintaan));

        $response->assertRedirect(route('permintaan.show', $permintaan));

        $stokKlinik = StokBarang::where('id_barang', $barang->id_obat)
            ->where('id_lokasi', $lokasiKlinik->id)
            ->first();

        $this->assertNotNull($stokKlinik);
        $this->assertSame(88, $stokKlinik->jumlah);

        $history = StokHistory::where('id_barang', $barang->id_obat)
            ->where('id_lokasi', $lokasiKlinik->id)
            ->latest()
            ->first();

        $this->assertNotNull($history);
        $this->assertSame(-12, $history->perubahan);
        $this->assertSame(100, $history->stok_sebelum);
        $this->assertSame(88, $history->stok_sesudah);

        $barang->refresh();
        $totalStokLokasi = StokBarang::where('id_barang', $barang->id_obat)->sum('jumlah');
        $this->assertSame($totalStokLokasi, $barang->stok);

        $permintaan->refresh();
        $this->assertSame(PermintaanBarang::STATUS_DIPENUHI, $permintaan->status);
    }
}
