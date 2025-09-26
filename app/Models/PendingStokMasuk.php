<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingStokMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_permintaan',
        'id_detail_permintaan',
        'id_barang',
        'id_lokasi',
        'jumlah_kemasan',
        'isi_per_kemasan',
        'satuan_kemasan',
        'tanggal_masuk',
        'expired_at',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'expired_at' => 'date',
    ];

    // Relasi ke permintaan
    public function permintaan()
    {
        return $this->belongsTo(PermintaanBarang::class, 'id_permintaan');
    }

    // Relasi ke barang medis
    public function barang()
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang', 'id_obat');
    }

    // Relasi ke lokasi
    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi');
    }

    // Relasi ke user yang input
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke detail permintaan
    public function detailPermintaan()
    {
        return $this->belongsTo(DetailPermintaanBarang::class, 'id_detail_permintaan');
    }

    // Hitung total dalam satuan terkecil
    public function getTotalSatuanTerkecilAttribute()
    {
        return $this->jumlah_kemasan * $this->isi_per_kemasan;
    }
}
