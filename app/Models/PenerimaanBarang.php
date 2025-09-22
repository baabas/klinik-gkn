<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PenerimaanBarangDetail;

class PenerimaanBarang extends Model
{
    protected $table = 'penerimaan_barang';

    protected $fillable = [
        'tanggal_penerimaan',
        'nomor_faktur',
        'supplier',
        'created_by',
        'id_lokasi',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_penerimaan' => 'date'
    ];

    public function detailPenerimaan()
    {
        return $this->hasMany(PenerimaanBarangDetail::class, 'id_penerimaan');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi');
    }
}