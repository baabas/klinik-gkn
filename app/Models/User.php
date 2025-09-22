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
     * [PERBAIKAN UTAMA] Relasi one-to-many ke model RekamMedis.
     * Sekarang bisa mengambil data berdasarkan NIP atau NIK dengan benar.
     */
    public function rekamMedis()
    {
        if ($this->nip) {
            // Jika user memiliki NIP (pasien karyawan)
            return $this->hasMany(RekamMedis::class, 'nip_pasien', 'nip');
        } else {
            // Jika user tidak memiliki NIP (pasti pasien non-karyawan)
            return $this->hasMany(RekamMedis::class, 'nik_pasien', 'nik');
        }
    }

    /**
     * [PERBAIKAN UTAMA] Relasi one-to-many ke model Checkup.
     * Sekarang bisa mengambil data berdasarkan NIP atau NIK dengan benar.
     */
    public function checkups()
    {
        if ($this->nip) {
            // Jika user memiliki NIP (pasien karyawan)
            return $this->hasMany(Checkup::class, 'nip_pasien', 'nip');
        } else {
            // Jika user tidak memiliki NIP (pasti pasien non-karyawan)
            return $this->hasMany(Checkup::class, 'nik_pasien', 'nik');
        }
    }

    /**
     * Relasi ke model LokasiKlinik.
     */
    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi');
    }
}