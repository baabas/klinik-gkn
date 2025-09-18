<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'expired_at' => 'date',
    ];

    /**
     * Relasi ke lokasi klinik.
     */
    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi', 'id');
    }

    /**
     * Relasi ke master barang medis.
     */
    public function barang()
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang', 'id_obat');
    }

    /**
     * Relasi ke pengguna yang melakukan transaksi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
