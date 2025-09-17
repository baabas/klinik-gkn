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
}
