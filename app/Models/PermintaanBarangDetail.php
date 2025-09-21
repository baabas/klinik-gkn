<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanBarangDetail extends Model
{
    use HasFactory;

    protected $table = 'permintaan_barang_detail';

    protected $fillable = [
        'permintaan_id',
        'barang_id',
        'barang_kemasan_id',
        'nama_barang_baru',
        'jumlah',
        'total_unit',
        'satuan',
        'kemasan',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'total_unit' => 'integer',
    ];

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(PermintaanBarang::class, 'permintaan_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangMedis::class, 'barang_id', 'id_obat');
    }

    public function barangMedis(): BelongsTo
    {
        return $this->belongsTo(BarangMedis::class, 'barang_id', 'id_obat');
    }

    public function kemasan(): BelongsTo
    {
        return $this->belongsTo(BarangKemasan::class, 'barang_kemasan_id');
    }

    public function getNamaBarangAttribute(): string
    {
        if ($this->barang) {
            return $this->barang->nama_obat;
        }

        return (string) $this->nama_barang_baru;
    }

    public function getIsBarangBaruAttribute(): bool
    {
        return empty($this->barang_id);
    }

    public function getKonversiLabelAttribute(): ?string
    {
        if (! $this->total_unit) {
            return null;
        }

        $unit = $this->barang?->satuan_dasar ?? $this->satuan;

        return number_format((int) $this->total_unit).' '.($unit ?? '');
    }
}
