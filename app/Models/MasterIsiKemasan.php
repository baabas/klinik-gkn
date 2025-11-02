<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterIsiKemasan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_isi_kemasan';
    protected $primaryKey = 'id_isi_kemasan';

    protected $fillable = [
        'nama_isi_kemasan',
        'singkatan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
