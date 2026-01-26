<?php

namespace Tests\Feature;

use App\Models\BarangMedis;
use App\Models\LokasiKlinik;
use App\Models\StokBarang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinimumStockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that barang_medis can be created with stok_minimal.
     */
    public function test_barang_medis_can_have_minimum_stock(): void
    {
        $barang = BarangMedis::factory()->create([
            'stok_minimal' => 50,
        ]);

        $this->assertEquals(50, $barang->stok_minimal);
        $this->assertDatabaseHas('barang_medis', [
            'id_obat' => $barang->id_obat,
            'stok_minimal' => 50,
        ]);
    }

    /**
     * Test that stock distribution respects minimum stock.
     */
    public function test_stock_distribution_prevents_going_below_minimum(): void
    {
        // Create necessary data
        $lokasi1 = LokasiKlinik::factory()->create(['nama_lokasi' => 'GKN 1']);
        $lokasi2 = LokasiKlinik::factory()->create(['nama_lokasi' => 'GKN 2']);

        $barang = BarangMedis::factory()->create([
            'stok_minimal' => 50,
        ]);

        $stok = StokBarang::create([
            'id_barang' => $barang->id_obat,
            'id_lokasi' => $lokasi1->id,
            'jumlah' => 60, // 60 units in stock
        ]);

        // Try to distribute 15 units (would leave 45, which is below minimum of 50)
        // This should fail
        $user = User::factory()->create([
            'role' => 'PENGADAAN',
            'id_lokasi' => $lokasi1->id,
        ]);

        $response = $this->actingAs($user)->post(route('barang-medis.distribusi', $barang->id_obat), [
            'jumlah' => 15,
            'lokasi_asal' => $lokasi1->id,
            'lokasi_tujuan' => $lokasi2->id,
        ]);

        // Should redirect back with error
        $response->assertSessionHasErrors();

        // Stock should remain unchanged
        $this->assertEquals(60, $stok->fresh()->jumlah);
    }

    /**
     * Test that stock distribution allows going below minimum if minimum is 0.
     */
    public function test_stock_distribution_allows_if_no_minimum_set(): void
    {
        $lokasi1 = LokasiKlinik::factory()->create(['nama_lokasi' => 'GKN 1']);
        $lokasi2 = LokasiKlinik::factory()->create(['nama_lokasi' => 'GKN 2']);

        $barang = BarangMedis::factory()->create([
            'stok_minimal' => 0, // No minimum
        ]);

        $stok = StokBarang::create([
            'id_barang' => $barang->id_obat,
            'id_lokasi' => $lokasi1->id,
            'jumlah' => 60,
        ]);

        $user = User::factory()->create([
            'role' => 'PENGADAAN',
            'id_lokasi' => $lokasi1->id,
        ]);

        $response = $this->actingAs($user)->post(route('barang-medis.distribusi', $barang->id_obat), [
            'jumlah' => 55,
            'lokasi_asal' => $lokasi1->id,
            'lokasi_tujuan' => $lokasi2->id,
        ]);

        // Should succeed
        $response->assertSessionHasNoErrors();

        // Stock should be reduced
        $this->assertEquals(5, $stok->fresh()->jumlah);
    }
}
