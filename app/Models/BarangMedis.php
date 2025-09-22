<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class BarangMedis extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'barang_medis';

    /**
     * Primary key untuk model ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_obat';

    /**
     * Kolom yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'tipe', // Kolom baru kita
        'satuan_dasar',
        'created_by',
        'stok',
        'issue_policy',
        'min_issue_unit',
        'rounding_rule',
    ];

    protected $casts = [
        'stok' => 'integer',
        'issue_policy' => 'string',
        'min_issue_unit' => 'string',
        'rounding_rule' => 'string',
    ];

    // --- RELASI ---

    /**
     * Relasi ke stok barang per lokasi (satu barang bisa ada di banyak stok lokasi).
     */
    public function stokLokasi(): HasMany
    {
        return $this->hasMany(StokBarang::class, 'id_barang', 'id_obat');
    }

    public function kemasanBarang(): HasMany
    {
        return $this->hasMany(BarangKemasan::class, 'barang_id', 'id_obat');
    }

    public function defaultKemasan(): HasOne
    {
        return $this->hasOne(BarangKemasan::class, 'barang_id', 'id_obat')
            ->where('is_default', true);
    }

    public function kemasanDefault(): ?BarangKemasan
    {
        if ($this->relationLoaded('kemasanBarang')) {
            return $this->kemasanBarang->firstWhere('is_default', true);
        }

        if ($this->relationLoaded('defaultKemasan')) {
            return $this->defaultKemasan;
        }

        return $this->defaultKemasan()->first();
    }

    public static function generateKode(string $tipe): string
    {
        $prefixMap = [
            'OBAT' => 'OBT',
            'ALKES' => 'ALK',
        ];

        $prefix = $prefixMap[$tipe] ?? 'BRG';

        $lastKode = static::where('tipe', $tipe)
            ->orderByDesc('id_obat')
            ->value('kode_obat');

        $lastNumber = 0;

        if ($lastKode && preg_match('/(\d+)$/', $lastKode, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        $nextNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);

        return sprintf('%s-%s', $prefix, $nextNumber);
    }

    /**
     * Seluruh riwayat stok untuk barang ini.
     */
    public function stokHistories(): HasMany
    {
        return $this->hasMany(StokHistory::class, 'id_barang', 'id_obat');
    }

    /**
     * Riwayat stok masuk (nilai perubahan positif).
     */
    public function stokMasuk(): HasMany
    {
        return $this->stokHistories()
            ->where('perubahan', '>', 0)
            ->whereNotNull('jumlah_kemasan');
    }

    /**
     * Riwayat stok masuk terakhir.
     */
    public function stokMasukTerakhir(): HasOne
    {
        return $this->hasOne(StokHistory::class, 'id_barang', 'id_obat')
            ->where('perubahan', '>', 0)
            ->whereNotNull('jumlah_kemasan')
            ->orderByDesc('tanggal_transaksi')
            ->orderByDesc('created_at');
    }

    /**
     * User yang membuat data master barang medis.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function applyIssuePolicy(int $requestedBase, bool $allowSplitOverride = false): array
    {
        $requestedBase = max(0, $requestedBase);

        $this->loadMissing('kemasanBarang');

        $issuePolicy = $allowSplitOverride ? 'ALLOW_SPLIT' : ($this->issue_policy ?? 'ALLOW_SPLIT');
        $minIssueUnit = $allowSplitOverride ? 'base' : ($this->min_issue_unit ?? 'base');
        $roundingRule = $this->rounding_rule ?? 'UP';

        if ($requestedBase === 0) {
            return [
                'approved_base' => 0,
                'kemasan_id' => null,
                'note' => null,
            ];
        }

        if ($issuePolicy === 'ALLOW_SPLIT' && $minIssueUnit === 'base') {
            return [
                'approved_base' => $requestedBase,
                'kemasan_id' => null,
                'note' => null,
            ];
        }

        $targetKemasan = null;

        if ($minIssueUnit !== 'base') {
            $level = $minIssueUnit === 'strip' ? 1 : 2;
            $targetKemasan = $this->kemasanBarang->firstWhere('level', $level);
        }

        if (! $targetKemasan) {
            $targetKemasan = $this->kemasanBarang->firstWhere('is_default', true)
                ?? $this->kemasanBarang->sortByDesc('isi_per_kemasan')->first();
        }

        if (! $targetKemasan || (int) $targetKemasan->isi_per_kemasan <= 0) {
            return [
                'approved_base' => $requestedBase,
                'kemasan_id' => null,
                'note' => null,
            ];
        }

        $isiPerKemasan = (int) $targetKemasan->isi_per_kemasan;
        $requestedPacks = $requestedBase / $isiPerKemasan;

        $roundedPacks = match ($roundingRule) {
            'DOWN' => (int) floor($requestedPacks),
            'NEAREST' => (int) round($requestedPacks, 0, PHP_ROUND_HALF_UP),
            default => (int) ceil($requestedPacks),
        };

        if ($roundedPacks === 0 && $requestedBase > 0 && $roundingRule !== 'DOWN') {
            $roundedPacks = 1;
        }

        $approved = $roundedPacks * $isiPerKemasan;

        if ($roundingRule === 'DOWN' && $roundedPacks === 0) {
            $approved = 0;
        }

        $note = null;

        if ($approved !== $requestedBase) {
            $packName = $targetKemasan->nama_kemasan ?? ($minIssueUnit !== 'base' ? $minIssueUnit : 'kemasan');
            $baseUnit = Str::lower($this->satuan_dasar ?? 'unit');
            $note = sprintf(
                'rounded %s to %s %s (%s %s)',
                strtoupper($roundingRule),
                number_format(max($roundedPacks, 0)),
                $packName,
                number_format(max($approved, 0)),
                $baseUnit
            );
        }

        return [
            'approved_base' => (int) max($approved, 0),
            'kemasan_id' => $approved > 0 ? (int) $targetKemasan->id : null,
            'note' => $note,
        ];
    }
}
