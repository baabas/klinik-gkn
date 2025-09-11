<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_karyawan',
        'nip',
        'email',
        'password',
        'akses',
    ];

    /**
     * Atribut yang harus disembunyikan saat serialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi many-to-many ke model Role.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Memeriksa apakah user memiliki role tertentu.
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        if (is_array($role)) {
            foreach ($role as $r) {
                if ($this->hasRole($r)) {
                    return true;
                }
            }
            return false;
        }

        return false;
    }

    /**
     * Relasi ke model Karyawan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nip', 'nip');
    }

    /**
     * Relasi one-to-many ke model RekamMedis.
     * Menggunakan 'nip_pasien' sebagai foreign key yang sudah diseragamkan.
     */
    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'nip_pasien', 'nip');
    }

    /**
     * Relasi one-to-many ke model Checkup.
     * Menggunakan 'nip_pasien' sebagai foreign key yang sudah diseragamkan.
     */
    public function checkups()
    {
        return $this->hasMany(Checkup::class, 'nip_pasien', 'nip');
    }
}