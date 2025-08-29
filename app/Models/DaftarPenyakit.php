<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarPenyakit extends Model
{
    use HasFactory;

    protected $table = 'daftar_penyakit';
    protected $primaryKey = 'kode_penyakit';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
