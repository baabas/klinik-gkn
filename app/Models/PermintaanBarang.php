<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanBarang extends Model
{
    use HasFactory;

    protected $table = 'permintaan_barang';

    protected $fillable = [
        'kode_permintaan',
        'id_lokasi_peminta',
        'id_user_peminta',
        'tanggal_permintaan',
        'catatan',
        'status',
    ];

    public const STATUS_PENDING = 'PENDING';
    public const STATUS_APPROVED = 'APPROVED';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_REJECTED = 'REJECTED';

    protected const STATUS_LABELS = [
        self::STATUS_PENDING => 'Menunggu',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    protected const STATUS_BADGE_CLASSES = [
        self::STATUS_PENDING => 'bg-warning text-dark',
        self::STATUS_APPROVED => 'bg-info',
        self::STATUS_COMPLETED => 'bg-success',
        self::STATUS_REJECTED => 'bg-danger',
    ];

    /**
     * Get the available status labels.
     */
    public static function statusLabels(): array
    {
        return self::STATUS_LABELS;
    }

    /**
     * Get the localized label for the current status.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst(strtolower($this->status));
    }

    /**
     * Get the bootstrap badge classes for the current status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return self::STATUS_BADGE_CLASSES[$this->status] ?? 'bg-secondary';
    }

    // --- RELASI ---

    /**
     * Relasi ke detail permintaan (satu permintaan punya banyak item barang).
     */
    public function detail()
    {
        return $this->hasMany(DetailPermintaanBarang::class, 'id_permintaan', 'id');
    }

    /**
     * Relasi ke lokasi klinik yang meminta.
     */
    public function lokasiPeminta()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi_peminta', 'id');
    }

    /**
     * Relasi ke user yang membuat permintaan.
     */
    public function userPeminta()
    {
        return $this->belongsTo(User::class, 'id_user_peminta', 'id');
    }
}
