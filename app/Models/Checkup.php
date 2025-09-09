<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkup extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'tanggal_pemeriksaan',
        'tekanan_darah',
        'gula_darah',
        'kolesterol',
        'asam_urat',
        'berat_badan',
        'tinggi_badan',
        'indeks_massa_tubuh',
        'lingkar_perut',
        'nama_sa',
        'jenis_kelamin_sa',
    ];

    /**
     * Relasi ke model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}