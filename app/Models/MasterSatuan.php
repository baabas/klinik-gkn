<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSatuan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_satuan';
    protected $primaryKey = 'id_satuan';

    protected $fillable = [
        'nama_satuan',
        'singkatan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
