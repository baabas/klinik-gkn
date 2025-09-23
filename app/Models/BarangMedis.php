<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
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
    ];

    protected $casts = [
        'stok' => 'integer',
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

    public function getKemasanDefaultAttribute(): ?BarangKemasan
    {
        if ($this->relationLoaded('kemasanBarang')) {
            return $this->kemasanBarang->firstWhere('is_default', true);
        }

        if ($this->relationLoaded('defaultKemasan')) {
            return $this->defaultKemasan;
        }

        return $this->defaultKemasan()->first();
    }

    public function breakdownPacks(int $qtyBase): array
    {
        $qtyBase = max(0, $qtyBase);

        $kemasan = $this->relationLoaded('kemasanBarang')
            ? $this->kemasanBarang
            : $this->kemasanBarang()->get();

        if (! $kemasan instanceof Collection) {
            $kemasan = collect($kemasan);
        }

        $sortedKemasan = $kemasan
            ->filter(fn (BarangKemasan $item) => (int) $item->isi_per_kemasan > 0)
            ->sortByDesc('isi_per_kemasan')
            ->values();

        $remaining = $qtyBase;
        $result = [];

        foreach ($sortedKemasan->take(2) as $item) {
            $isi = (int) $item->isi_per_kemasan;

            if ($isi <= 0) {
                continue;
            }

            $key = Str::slug($item->nama_kemasan, '_');
            $result[$key] = intdiv($remaining, $isi);
            $remaining %= $isi;
        }

        $result['base'] = $remaining;

        return $result;
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
}
