<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class StokHistory extends Model
{
    use HasFactory;

    protected $table = 'stok_histories';

    protected $fillable = [
        'id_barang',
        'id_lokasi',
        'perubahan',
        'keterangan',
        'stok_sebelum',
        'stok_sesudah',
        'user_id',
        'tanggal_transaksi',
        'jumlah_kemasan',
        'isi_per_kemasan',
        'satuan_kemasan',
        'expired_at',
        'kemasan_id',
        'base_unit',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'expired_at' => 'date',
        'perubahan' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
        'jumlah_kemasan' => 'integer',
        'isi_per_kemasan' => 'integer',
    ];

    /**
     * Relasi ke lokasi klinik.
     */
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi', 'id');
    }

    /**
     * Relasi ke master barang medis.
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang', 'id_obat');
    }

    /**
     * Relasi ke pengguna yang melakukan transaksi.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kemasan(): BelongsTo
    {
        return $this->belongsTo(BarangKemasan::class, 'kemasan_id');
    }
}
