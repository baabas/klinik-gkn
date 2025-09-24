<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    use HasFactory;

    protected $table = 'permintaan_barang';

    protected $fillable = [
        'kode_permintaan',
        'id_lokasi_peminta',
        'id_user_peminta',
        'tanggal_permintaan',
        'catatan',
        'status',
    ];

    // --- RELASI ---

    /**
     * Relasi ke detail permintaan (satu permintaan punya banyak item barang).
     */
    public function detail()
    {
        return $this->hasMany(DetailPermintaanBarang::class, 'id_permintaan', 'id');
    }

    /**
     * Relasi ke lokasi klinik yang meminta.
     */
    public function lokasiPeminta()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi_peminta', 'id');
    }

    /**
     * Relasi ke user yang membuat permintaan.
     */
    public function userPeminta()
    {
        return $this->belongsTo(User::class, 'id_user_peminta', 'id');
    }
}
