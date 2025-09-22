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

    public static function boot()
    {
        parent::boot();

        // Validasi sebelum menyimpan
        static::saving(function ($lokasi) {
            // Hanya izinkan ID 1 dan 2
            if ($lokasi->id && !in_array($lokasi->id, [1, 2])) {
                throw new \Exception('Hanya ID lokasi 1 dan 2 yang diperbolehkan.');
            }
        });
    }

    // Scope untuk membatasi query hanya ke lokasi 1 dan 2
    public function scopeValid($query)
    {
        return $query->whereIn('id', [1, 2]);
    }

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
