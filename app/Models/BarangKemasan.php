<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKemasan extends Model
{
    use HasFactory;

    protected $table = 'barang_kemasan';

    protected $fillable = [
        'barang_id',
        'nama_kemasan',
        'isi_per_kemasan',
        'is_default',
        'level',
    ];

    protected $casts = [
        'isi_per_kemasan' => 'integer',
        'is_default' => 'boolean',
        'level' => 'integer',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangMedis::class, 'barang_id', 'id_obat');
    }
}
