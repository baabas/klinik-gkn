<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_rekam_medis';
    protected $table = 'rekam_medis';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nip_pasien',        // [DITAMBAHKAN]
        'id_dokter',         // [DITAMBAHKAN]
        'tanggal_kunjungan',
        'anamnesa',
        'terapi',
        'nama_sa',
        'jenis_kelamin_sa',
    ];

    /**
     * Relasi ke model User (Dokter).
     */
    public function dokter()
    {
        return $this->belongsTo(User::class, 'id_dokter');
    }

    /**
     * Relasi ke model User (Pasien).
     */
    public function pasien()
    {
        return $this->belongsTo(User::class, 'nip_pasien', 'nip');
    }

    /**
     * Relasi one-to-many ke DetailDiagnosa.
     */
    public function detailDiagnosa()
    {
        return $this->hasMany(DetailDiagnosa::class, 'id_rekam_medis', 'id_rekam_medis');
    }

    /**
     * Relasi one-to-many ke ResepObat.
     */
    public function resepObat()
    {
        return $this->hasMany(ResepObat::class, 'id_rekam_medis', 'id_rekam_medis');
    }
}