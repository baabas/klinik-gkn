<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $table = 'rekam_medis';
    protected $primaryKey = 'id_rekam_medis';
    public $timestamps = false;

    protected $fillable = [
        'id_pasien', 'nip', 'tanggal_kunjungan', 'riwayat_sakit',
        'pengobatan', 'nama_sa', 'jenis_kelamin_sa'
    ];

    public function detailDiagnosa()
    {
        return $this->hasMany(DetailDiagnosa::class, 'id_rekam_medis', 'id_rekam_medis');
    }

    public function resepObat()
    {
        return $this->hasMany(ResepObat::class, 'id_rekam_medis', 'id_rekam_medis');
    }
}
