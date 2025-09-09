<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPermintaanBarang extends Model
{
    use HasFactory;

    protected $table = 'detail_permintaan_barang';

    protected $fillable = [
        'id_permintaan',
        'id_barang',
        'jumlah_diminta',
        'jumlah_disetujui',
        'catatan',
        'nama_barang_baru',
        'tipe_barang_baru',
        'satuan_barang_baru',
    ];

    // --- RELASI ---

    /**
     * Relasi ke header permintaan (satu detail milik satu permintaan).
     */
    public function permintaan()
    {
        return $this->belongsTo(PermintaanBarang::class, 'id_permintaan', 'id');
    }

    /**
     * Relasi ke master barang medis.
     */
    public function barangMedis()
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang', 'id_obat');
    }
}
