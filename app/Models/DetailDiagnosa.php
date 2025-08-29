<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailDiagnosa extends Model
{
    use HasFactory;
    protected $table = 'detail_diagnosa';
    protected $primaryKey = 'id_detail_diagnosa';
    public $timestamps = false;

    protected $fillable = ['kode_penyakit'];

    public function penyakit()
    {
        return $this->belongsTo(DaftarPenyakit::class, 'kode_penyakit', 'kode_penyakit');
    }
}
