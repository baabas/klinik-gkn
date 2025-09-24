<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BarangMedis extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'barang_medis';

    /**
     * Primary key untuk model ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_obat';

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'satuan',
        'kemasan',
        'kategori_barang',
        'isi_kemasan_jumlah',
        'isi_kemasan_satuan',
        'isi_per_satuan',
        'satuan_terkecil',
    ];

    // --- RELASI ---

    /**
     * Relasi ke stok barang (satu barang bisa ada di banyak stok lokasi).
     */
    public function stok(): HasMany
    {
        return $this->hasMany(StokBarang::class, 'id_barang', 'id_obat');
    }

    /**
     * Seluruh riwayat stok untuk barang ini.
     */
    public function stokHistories(): HasMany
    {
        return $this->hasMany(StokHistory::class, 'id_barang', 'id_obat');
    }

    /**
     * Riwayat stok masuk (nilai perubahan positif).
     */
    public function stokMasuk(): HasMany
    {
        return $this->stokHistories()->where('perubahan', '>', 0);
    }

    /**
     * Riwayat stok masuk terakhir.
     */
    public function stokMasukTerakhir(): HasOne
    {
        return $this->hasOne(StokHistory::class, 'id_barang', 'id_obat')
            ->where('perubahan', '>', 0)
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at');
    }

    /**
     * Riwayat stok masuk untuk bulan ini.
     */
    public function stokMasukBulanIni(): HasMany
    {
        return $this->hasMany(StokHistory::class, 'id_barang', 'id_obat')
            ->where('perubahan', '>', 0)
            ->whereYear('tanggal_transaksi', now()->year)
            ->whereMonth('tanggal_transaksi', now()->month);
    }
}
