<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Menonaktifkan timestamps (created_at dan updated_at) karena tidak kita butuhkan.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Relasi many-to-many ke model User.
     * Satu peran bisa dimiliki oleh banyak user.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
