<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangMedis extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'barang_medis';

    /**
     * Primary key untuk model ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_obat';

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'tipe', // Kolom baru kita
        'satuan',
        'kemasan',
        'isi_per_kemasan',
        'unit_kemasan',
        'satuan_terkecil',
    ];

    // --- RELASI ---

    /**
     * Relasi ke stok barang (satu barang bisa ada di banyak stok lokasi).
     */
    public function stok()
    {
        return $this->hasMany(StokBarang::class, 'id_barang', 'id_obat');
    }

     /**
     * Format teks kemasan: "1 unit = N satuan kecil".
     */
    public function getDeskripsiKemasanAttribute(): string
    {
        $unitKemasan = $this->unit_kemasan ?: $this->kemasan ?: $this->satuan;
        $satuanTerkecil = $this->satuan_terkecil ?: $this->satuan;
        $isiPerKemasan = (int) ($this->isi_per_kemasan ?? 1);

        return sprintf('1 %s = %d %s', $unitKemasan, max(1, $isiPerKemasan), $satuanTerkecil);
    }
}
