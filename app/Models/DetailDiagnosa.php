<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDiagnosa extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_detail_diagnosa';
    protected $table = 'detail_diagnosa';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_rekam_medis',
        'ICD10', // [DIPERBAIKI] Mengganti 'kode_penyakit' menjadi 'ICD10'
    ];

    /**
     * Relasi ke model DaftarPenyakit.
     */
    public function penyakit()
    {
        return $this->belongsTo(DaftarPenyakit::class, 'ICD10', 'ICD10');
    }

    /**
     * Relasi ke model RekamMedis.
     */
    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'id_rekam_medis', 'id_rekam_medis');
    }
}