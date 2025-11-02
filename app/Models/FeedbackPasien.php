<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackPasien extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'feedback_pasien';

    /**
     * Primary key dari tabel.
     *
     * @var string
     */
    protected $primaryKey = 'id_feedback';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_rekam_medis',
        'nip_pasien',
        'nik_pasien',
        'rating',
        'komentar',
        'waktu_feedback',
    ];

    /**
     * Atribut yang harus di-cast ke tipe data tertentu.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'waktu_feedback' => 'datetime',
        'rating' => 'integer',
    ];

    /**
     * Relasi ke model RekamMedis.
     * Setiap feedback terkait dengan satu rekam medis.
     *
     * @return BelongsTo
     */
    public function rekamMedis(): BelongsTo
    {
        return $this->belongsTo(RekamMedis::class, 'id_rekam_medis', 'id_rekam_medis');
    }

    /**
     * Relasi ke model User sebagai pasien karyawan.
     *
     * @return BelongsTo
     */
    public function pasienKaryawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'nip_pasien', 'nip');
    }

    /**
     * Relasi ke model NonKaryawan sebagai pasien non-karyawan.
     *
     * @return BelongsTo
     */
    public function pasienNonKaryawan(): BelongsTo
    {
        return $this->belongsTo(NonKaryawan::class, 'nik_pasien', 'nik');
    }

    /**
     * Scope untuk filter feedback berdasarkan tanggal.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $date
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('waktu_feedback', $date);
    }

    /**
     * Scope untuk filter feedback berdasarkan rating.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $rating
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Accessor untuk mendapatkan label rating.
     *
     * @return string
     */
    public function getRatingLabelAttribute(): string
    {
        return match($this->rating) {
            1 => 'Sangat Tidak Puas',
            2 => 'Tidak Puas',
            3 => 'Cukup',
            4 => 'Puas',
            5 => 'Sangat Puas',
            default => 'Unknown'
        };
    }

    /**
     * Accessor untuk mendapatkan emoji rating.
     *
     * @return string
     */
    public function getRatingEmojiAttribute(): string
    {
        return match($this->rating) {
            1 => '😡',
            2 => '😞',
            3 => '😐',
            4 => '😊',
            5 => '😍',
            default => '❓'
        };
    }
}
