<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StokBatch extends Model
{
    protected $table = 'stok_batch';

    protected $fillable = [
        'id_barang',
        'id_lokasi',
        'nomor_batch',
        'tanggal_kadaluarsa',
        'jumlah_unit',
        'created_by',
        'supplier',
        'nomor_faktur',
        'tanggal_penerimaan'
    ];

    protected $casts = [
        'tanggal_kadaluarsa' => 'date',
        'tanggal_penerimaan' => 'date'
    ];

    public function barang()
    {
        return $this->belongsTo(BarangMedis::class, 'id_barang', 'id_obat');
    }

    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusExpAttribute()
    {
        $today = Carbon::today();
        $expDate = $this->tanggal_kadaluarsa;
        $monthsUntilExp = $today->diffInMonths($expDate, false);

        if ($monthsUntilExp < 0) {
            return 'EXPIRED';
        } elseif ($monthsUntilExp <= 3) {
            return 'WARNING';
        } else {
            return 'OK';
        }
    }
}