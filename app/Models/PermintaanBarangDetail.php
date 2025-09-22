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
        'kemasan_id',
        'nama_barang_baru',
        'jumlah',
        'jumlah_kemasan',
        'isi_per_kemasan',
        'total_unit',
        'total_unit_dasar',
        'satuan',
        'base_unit',
        'kemasan',
        'satuan_kemasan',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'jumlah_kemasan' => 'integer',
        'isi_per_kemasan' => 'integer',
        'total_unit' => 'integer',
        'total_unit_dasar' => 'integer',
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
        return $this->belongsTo(BarangKemasan::class, 'kemasan_id');
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
        $total = $this->total_unit_dasar ?? $this->total_unit;

        if (! $total) {
            return null;
        }

        $unit = $this->base_unit ?? $this->barang?->satuan_dasar ?? $this->satuan;

        return trim('≈ '.number_format((int) $total).' '.($unit ?? ''));
    }

    public function getJumlahKemasanLabelAttribute(): ?string
    {
        if (! $this->barang_id) {
            return null;
        }

        $jumlahKemasan = $this->jumlah_kemasan ?? ($this->jumlah !== null ? (int) $this->jumlah : null);
        $kemasan = $this->satuan_kemasan
            ?? $this->kemasan
            ?? $this->kemasan()->value('nama_kemasan');
        $total = $this->total_unit_dasar ?? $this->total_unit;
        $baseUnit = $this->base_unit ?? $this->barang?->satuan_dasar;

        $parts = [];

        if ($jumlahKemasan !== null) {
            $parts[] = number_format((int) $jumlahKemasan);
        }

        if ($kemasan) {
            $parts[] = $kemasan;
        }

        $label = trim(implode(' ', $parts));

        if ($total) {
            $label .= ' (≈ '.number_format((int) $total);
            if ($baseUnit) {
                $label .= ' '.$baseUnit;
            }
            $label .= ')';
        }

        return trim($label);
    }
}
