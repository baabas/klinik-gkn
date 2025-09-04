<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\CustomResetPasswordNotification;
use App\Models\Role;
use App\Models\LokasiKlinik;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nip',
        'nama_karyawan',
        'email',
        'password',
        'id_lokasi', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nip', 'nip');
    }

    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'id_pasien', 'id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    // --- FUNGSI PENTING UNTUK ROLE DAN LOKASI ---

    /**
     * Relasi ke LokasiKlinik.
     */
    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi', 'id');
    }

    /**
     * Relasi many-to-many ke Role.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Fungsi pembantu untuk memeriksa role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
}
