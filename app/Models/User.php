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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nip',
        'nama_karyawan',
        'email',
        'password',
        'id_lokasi', 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
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
     * Relasi ke model Karyawan.
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nip', 'nip');
    }

    /**
     * Relasi ke model RekamMedis.
     */
    public function rekamMedis()
    {
        return $this->hasMany(RekamMedis::class, 'id_pasien', 'id')->orderBy('tanggal_kunjungan', 'desc');
    }

    /**
     * Relasi ke model Checkup.
     */
    public function checkups()
    {
        return $this->hasMany(Checkup::class)->orderBy('tanggal_pemeriksaan', 'desc');
    }

    /**
     * Mengirim notifikasi reset password kustom.
     */
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