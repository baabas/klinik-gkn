<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiKlinik extends Model
{
    use HasFactory;

    protected $table = 'lokasi_klinik';

    protected $fillable = [
        'nama_lokasi',
        'alamat',
    ];

    // --- RELASI ---

    /**
     * Relasi ke stok barang (satu lokasi punya banyak stok barang).
     */
    public function stok()
    {
        return $this->hasMany(StokBarang::class, 'id_lokasi', 'id');
    }

    public function permintaanBarang()
    {
        return $this->hasMany(PermintaanBarang::class, 'id_lokasi_peminta', 'id');
    }
}
