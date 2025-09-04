<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    use HasFactory;

    protected $table = 'stok_barang';

    protected $fillable = [
        'id_barang',
        'id_lokasi',
        'jumlah',
    ];

    // --- RELASI ---

    /**
     * Relasi ke master barang medis (satu stok milik satu barang).
     */
    public function barangMedis()
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang', 'id_obat');
    }

    /**
     * Relasi ke lokasi klinik (satu stok milik satu lokasi).
     */
    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi', 'id');
    }
}
