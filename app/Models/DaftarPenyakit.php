<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarPenyakit extends Model
{
    use HasFactory;

    protected $table = 'daftar_penyakit';
    protected $primaryKey = 'ICD10';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'ICD10',
        'nama_penyakit'
    ];

    // Relationship with DetailDiagnosa
    public function detailDiagnosa()
    {
        return $this->hasMany(DetailDiagnosa::class, 'ICD10', 'ICD10');
    }
}
