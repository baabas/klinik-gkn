<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanBarangDetail extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_barang_detail';
    protected $primaryKey = 'id_penerimaan_detail';
    
    protected $fillable = [
        'id_penerimaan',
        'id_barang',
        'jumlah_kemasan',
        'no_batch',
        'tanggal_kadaluarsa',
        'keterangan'
    ];

    public function penerimaan()
    {
        return $this->belongsTo(PenerimaanBarang::class, 'id_penerimaan');
    }

    public function barang()
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang');
    }
}