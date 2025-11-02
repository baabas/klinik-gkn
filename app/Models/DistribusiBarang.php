<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DistribusiBarang extends Model
{
    protected $table = 'distribusi_barang';
    protected $primaryKey = 'id_distribusi';

    protected $fillable = [
        'id_barang',
        'id_lokasi_asal',
        'id_lokasi_tujuan',
        'id_user',
        'jumlah',
        'keterangan',
        'status',
        'validated_by',
        'validated_at',
        'validation_note',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    /**
     * Relasi ke BarangMedis
     */
    public function barang()
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang', 'id_obat');
    }

    /**
     * Relasi ke LokasiKlinik (Asal)
     */
    public function lokasiAsal()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi_asal', 'id');
    }

    /**
     * Relasi ke LokasiKlinik (Tujuan)
     */
    public function lokasiTujuan()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi_tujuan', 'id');
    }

    /**
     * Relasi ke User (yang melakukan distribusi)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    /**
     * Relasi ke User (yang validasi - PENGADAAN)
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by', 'id');
    }
}
