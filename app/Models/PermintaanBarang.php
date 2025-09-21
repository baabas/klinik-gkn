<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class PermintaanBarang extends Model
{
    use HasFactory;

    protected $table = 'permintaan_barang';

    protected $fillable = [
        'kode',
        'peminta_id',
        'lokasi_id',
        'tanggal',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_DIAJUKAN = 'DIAJUKAN';
    public const STATUS_DISETUJUI = 'DISETUJUI';
    public const STATUS_DITOLAK = 'DITOLAK';
    public const STATUS_DIPENUHI = 'DIPENUHI';

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_DIAJUKAN => 'Diajukan',
        self::STATUS_DISETUJUI => 'Disetujui',
        self::STATUS_DITOLAK => 'Ditolak',
        self::STATUS_DIPENUHI => 'Dipenuhi',
    ];

    public const STATUS_BADGES = [
        self::STATUS_DRAFT => 'badge bg-secondary',
        self::STATUS_DIAJUKAN => 'badge bg-info text-dark',
        self::STATUS_DISETUJUI => 'badge bg-success',
        self::STATUS_DITOLAK => 'badge bg-danger',
        self::STATUS_DIPENUHI => 'badge bg-primary',
    ];

    public static function statusOptions(): array
    {
        return self::STATUS_LABELS;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return self::STATUS_BADGES[$this->status] ?? 'badge bg-secondary';
    }

    public function scopeStatus(\Illuminate\Database\Eloquent\Builder $query, ?string $status): \Illuminate\Database\Eloquent\Builder
    {
        if (! $status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, $search): Builder
    {
        if ($search === null) {
            return $query;
        }

        $search = trim((string) $search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('kode', 'like', "%{$search}%")
                ->orWhereHas('peminta', function ($sub) use ($search) {
                    $sub->where('nama_karyawan', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                })
                ->orWhereHas('lokasi', function ($sub) use ($search) {
                    $sub->where('nama_lokasi', 'like', "%{$search}%");
                });
        });
    }

    public function details(): HasMany
    {
        return $this->hasMany(PermintaanBarangDetail::class, 'permintaan_id');
    }

    public function peminta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peminta_id');
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(LokasiKlinik::class, 'lokasi_id');
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isDiajukan(): bool
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    public function isDisetujui(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    public static function generateKode(): string
    {
        $today = Carbon::now()->format('Ymd');
        $prefix = "REQ-{$today}-";

        $lastKode = static::where('kode', 'like', $prefix.'%')
            ->orderByDesc('kode')
            ->value('kode');

        $next = 1;
        if ($lastKode) {
            $number = (int) substr($lastKode, -4);
            $next = $number + 1;
        }

        return sprintf('%s%04d', $prefix, $next);
    }
}
