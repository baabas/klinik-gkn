<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepObat extends Model
{
    use HasFactory;
    protected $table = 'resep_obat';
    protected $primaryKey = 'id_resep_obat';
    public $timestamps = true; // Sebaiknya timestamps diaktifkan

    /**
     * ================== PERBAIKAN DI SINI ==================
     * Menghapus 'dosis' dari fillable
     */
    protected $fillable = [
        'id_rekam_medis',
        'id_obat',
        'kuantitas',
    ];
    // =======================================================

    public function obat()
    {
        return $this->belongsTo(BarangMedis::class, 'id_obat', 'id_obat');
    }
}