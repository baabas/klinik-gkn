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
        'nik',
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
        // Disederhanakan untuk efisiensi, karena hasRole() hanya dipanggil dengan string di kode Anda
        return false;
    }

    /**
     * Relasi ke profil Karyawan.
     */
    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'nip', 'nip');
    }
    
    /**
     * Relasi ke profil NonKaryawan.
     */
    public function nonKaryawan()
    {
        return $this->hasOne(NonKaryawan::class, 'nik', 'nik');
    }

    /**
     * Relasi ke rekam medis untuk pasien karyawan (berdasarkan NIP).
     */
    public function rekamMedisKaryawan()
    {
        return $this->hasMany(RekamMedis::class, 'nip_pasien', 'nip');
    }

    /**
     * Relasi ke rekam medis untuk pasien non-karyawan (berdasarkan NIK).
     */
    public function rekamMedisNonKaryawan()
    {
        return $this->hasMany(RekamMedis::class, 'nik_pasien', 'nik');
    }

    /**
     * Relasi gabungan untuk mendapatkan semua rekam medis (karyawan + non-karyawan).
     */
    public function rekamMedis()
    {
        // Gabungkan hasil dari kedua relasi
        if ($this->nip) {
            // Jika user memiliki NIP, ambil berdasarkan NIP
            return $this->rekamMedisKaryawan();
        } else {
            // Jika user tidak memiliki NIP, ambil berdasarkan NIK
            return $this->rekamMedisNonKaryawan();
        }
    }

    /**
     * Relasi ke checkup untuk pasien karyawan (berdasarkan NIP).
     */
    public function checkupKaryawan()
    {
        return $this->hasMany(Checkup::class, 'nip_pasien', 'nip');
    }

    /**
     * Relasi ke checkup untuk pasien non-karyawan (berdasarkan NIK).
     */
    public function checkupNonKaryawan()
    {
        return $this->hasMany(Checkup::class, 'nik_pasien', 'nik');
    }

    /**
     * Relasi gabungan untuk mendapatkan semua checkup (karyawan + non-karyawan).
     */
    public function checkups()
    {
        // Gabungkan hasil dari kedua relasi
        if ($this->nip) {
            // Jika user memiliki NIP, ambil berdasarkan NIP
            return $this->checkupKaryawan();
        } else {
            // Jika user tidak memiliki NIP, ambil berdasarkan NIK
            return $this->checkupNonKaryawan();
        }
    }
}