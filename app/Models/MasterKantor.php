<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKantor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_kantor';
    protected $primaryKey = 'id_kantor';

    protected $fillable = [
        'nama_kantor',
        'kode_kantor',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
