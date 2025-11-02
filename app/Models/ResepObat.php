<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepObat extends Model
{
    use HasFactory;
    protected $table = 'resep_obat';
    protected $primaryKey = 'id_resep_obat';
    public $timestamps = true; 


    protected $fillable = [
        'id_rekam_medis',
        'id_obat',
        'kuantitas',
        'jumlah',
        'dosis', // [BARU] Tambahan untuk dosis obat (contoh: "3x1", "2x1 setelah makan")
        'aturan_pakai',
    ];


    public function obat()
    {
        return $this->belongsTo(BarangMedis::class, 'id_obat', 'id_obat');
    }
}
