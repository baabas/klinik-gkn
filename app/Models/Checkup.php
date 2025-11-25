<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkup extends Model
{
    use HasFactory;

    /**
     * Primary key untuk tabel checkups.
     *
     * @var string
     */
    protected $primaryKey = 'id_checkup';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'nip_pasien',
        'nik_pasien', // Tambahkan untuk non-karyawan
        'id_dokter',
        'tanggal_pemeriksaan',
        'tekanan_darah',
        'gula_darah',
        'kolesterol',
        'asam_urat',
        'berat_badan',
        'tinggi_badan',
        'suhu_badan', // [BARU] Tambahan untuk suhu badan
        'indeks_massa_tubuh',
        'lingkar_perut',
        'nama_sa',
        'jenis_kelamin_sa',
    ];

    /**
     * Relasi ke model User sebagai pasien karyawan.
     */
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_pasien', 'nip');
    }

    /**
     * Relasi ke model User sebagai pasien non-karyawan.
     */
    public function pasienNonKaryawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nik_pasien', 'nik');
    }

    /**
     * Relasi ke model User sebagai dokter.
     */
    public function dokter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_dokter');
    }
}
