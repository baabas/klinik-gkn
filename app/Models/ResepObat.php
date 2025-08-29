<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResepObat extends Model
{
    use HasFactory;
    protected $table = 'resep_obat';
    protected $primaryKey = 'id_resep';
    public $timestamps = false;

    protected $fillable = ['id_obat', 'kuantitas'];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'id_obat', 'id_obat');
    }
}
