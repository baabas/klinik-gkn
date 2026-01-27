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
     * Threshold untuk stok kritis jika stok_minimal tidak diset (dalam satuan terkecil).
     */
    const FALLBACK_CRITICAL_THRESHOLD = 50;

    /**
     * Threshold untuk stok warning jika stok_minimal tidak diset (dalam satuan terkecil).
     */
    const FALLBACK_WARNING_THRESHOLD = 100;

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
        'stok_minimal',
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
     * Riwayat stok masuk (nilai perubahan positif dari input pengadaan).
     * Hanya menghitung barang masuk yang diinput oleh pengadaan,
     * exclude distribusi antar klinik dan penggunaan obat.
     */
    public function stokMasuk(): HasMany
    {
        return $this->stokHistories()
            ->where('perubahan', '>', 0)
            ->where(function ($query) {
                $query->where('keterangan', 'not like', '%Distribusi ke%')
                    ->where('keterangan', 'not like', '%Distribusi dari%')
                    ->where('keterangan', 'not like', '%Resep%')
                    ->where('keterangan', 'not like', '%Digunakan%')
                    ->where('keterangan', 'not like', '%Koreksi%');
            });
    }

    /**
     * Riwayat stok masuk terakhir.
     */
    public function stokMasukTerakhir(): HasOne
    {
        return $this->hasOne(StokHistory::class, 'id_barang', 'id_obat')
            ->where('perubahan', '>', 0)
            ->where(function ($query) {
                $query->where('keterangan', 'not like', '%Distribusi ke%')
                    ->where('keterangan', 'not like', '%Distribusi dari%')
                    ->where('keterangan', 'not like', '%Resep%')
                    ->where('keterangan', 'not like', '%Digunakan%')
                    ->where('keterangan', 'not like', '%Koreksi%');
            })
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at');
    }

    /**
     * Relasi ke stok barang berdasarkan lokasi tertentu.
     */
    public function stokByLokasi($idLokasi): HasMany
    {
        return $this->stok()->where('id_lokasi', $idLokasi);
    }

    /**
     * Riwayat stok masuk untuk bulan ini.
     * Hanya menghitung barang masuk yang diinput oleh pengadaan.
     */
    public function stokMasukBulanIni(): HasMany
    {
        return $this->hasMany(StokHistory::class, 'id_barang', 'id_obat')
            ->where('perubahan', '>', 0)
            ->where(function ($query) {
                $query->where('keterangan', 'not like', '%Distribusi ke%')
                    ->where('keterangan', 'not like', '%Distribusi dari%')
                    ->where('keterangan', 'not like', '%Resep%')
                    ->where('keterangan', 'not like', '%Digunakan%')
                    ->where('keterangan', 'not like', '%Koreksi%');
            })
            ->whereYear('tanggal_transaksi', now()->year)
            ->whereMonth('tanggal_transaksi', now()->month);
    }

    /**
     * Relasi ke detail permintaan barang.
     */
    public function detailPermintaans(): HasMany
    {
        return $this->hasMany(DetailPermintaanBarang::class, 'id_barang', 'id_obat');
    }

    /**
     * Hitung jumlah stok dalam satuan terkecil.
     * 
     * @param int $stokKemasan Jumlah stok dalam kemasan utama
     * @return int Jumlah stok dalam satuan terkecil
     */
    public function konversiKeStokTerkecil($stokKemasan)
    {
        $isiPerKemasan = ($this->isi_kemasan_jumlah ?? 1) * ($this->isi_per_satuan ?? 1);
        return $stokKemasan * $isiPerKemasan;
    }

    /**
     * Cek apakah stok termasuk kritis (di bawah atau sama dengan stok minimal).
     * Note: Kedua parameter sudah dalam satuan terkecil.
     * 
     * @param int $stokTerkecil Jumlah stok dalam satuan terkecil
     * @return bool True jika stok kritis
     */
    public function isStokKritis($stokTerkecil)
    {
        $stokMinimal = (int)($this->stok_minimal ?? 0);
        
        if ($stokMinimal <= 0) {
            return false;
        }
        
        // Kedua nilai sudah dalam satuan terkecil, langsung bandingkan
        return $stokTerkecil <= $stokMinimal;
    }

    /**
     * Dapatkan level stok (critical, warning, ok) berdasarkan stok minimal.
     * Note: Parameter stokTerkecil sudah dalam satuan terkecil.
     * 
     * @param int $stokTerkecil Jumlah stok dalam satuan terkecil
     * @return string 'critical', 'warning', atau 'ok'
     */
    public function getStokLevel($stokTerkecil)
    {
        $stokMinimal = (int)($this->stok_minimal ?? 0);
        
        if ($stokMinimal > 0) {
            // Kedua nilai sudah dalam satuan terkecil, langsung bandingkan
            if ($stokTerkecil <= $stokMinimal) {
                return 'critical';
            } elseif ($stokTerkecil <= $stokMinimal * 1.5) {
                return 'warning';
            }
        } else {
            // Fallback threshold jika stok_minimal tidak diset
            if ($stokTerkecil < self::FALLBACK_CRITICAL_THRESHOLD) {
                return 'critical';
            } elseif ($stokTerkecil < self::FALLBACK_WARNING_THRESHOLD) {
                return 'warning';
            }
        }
        
        return 'ok';
    }
}
