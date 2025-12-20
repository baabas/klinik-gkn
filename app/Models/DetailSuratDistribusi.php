<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailSuratDistribusi extends Model
{
    protected $table = 'detail_surat_distribusi';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_surat',
        'id_barang',
        'jumlah',
    ];

    /**
     * Relasi ke SuratDistribusi
     */
    public function surat(): BelongsTo
    {
        return $this->belongsTo(SuratDistribusi::class, 'id_surat', 'id_surat');
    }

    /**
     * Relasi ke BarangMedis
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang', 'id_obat');
    }
}
