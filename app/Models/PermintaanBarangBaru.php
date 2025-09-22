<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarangBaru extends Model
{
    use HasFactory;

    protected $table = 'permintaan_barang_baru';

    protected $fillable = [
        'id_pemohon',
        'id_lokasi',
        'status',
        'data_barang',
        'jumlah_permintaan',
        'alasan_permintaan',
        'catatan_pengadaan',
        'id_pengadaan'
    ];

    protected $casts = [
        'data_barang' => 'array'
    ];

    public function pemohon()
    {
        return $this->belongsTo(User::class, 'id_pemohon');
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi');
    }

    public function pengadaan()
    {
        return $this->belongsTo(User::class, 'id_pengadaan');
    }
}