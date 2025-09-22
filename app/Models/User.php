<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Collection; // Import Collection

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

    // --- PERBAIKAN UTAMA DIMULAI DI SINI ---

    /**
     * Relasi terpisah HANYA untuk rekam medis pasien KARYAWAN via NIP.
     */
    public function rekamMedisByNip()
    {
        return $this->hasMany(RekamMedis::class, 'nip_pasien', 'nip');
    }

    /**
     * Relasi terpisah HANYA untuk rekam medis pasien NON-KARYAWAN via NIK.
     */
    public function rekamMedisByNik()
    {
        return $this->hasMany(RekamMedis::class, 'nik_pasien', 'nik');
    }

    /**
     * Accessor yang dipanggil saat kita mengakses '$pasien->rekamMedis'.
     * Ini akan secara cerdas menggabungkan riwayat rekam medis.
     */
    public function getRekamMedisAttribute(): Collection
    {
        // Jika user ini adalah karyawan (punya NIP), ambil riwayat berdasarkan NIP.
        if ($this->nip) {
            return $this->rekamMedisByNip;
        }
        // Jika tidak, pasti non-karyawan, ambil riwayat berdasarkan NIK.
        return $this->rekamMedisByNik;
    }

    /**
     * Relasi terpisah HANYA untuk checkup pasien KARYAWAN via NIP.
     */
    public function checkupsByNip()
    {
        return $this->hasMany(Checkup::class, 'nip_pasien', 'nip');
    }

    /**
     * Relasi terpisah HANYA untuk checkup pasien NON-KARYAWAN via NIK.
     */
    public function checkupsByNik()
    {
        return $this->hasMany(Checkup::class, 'nik_pasien', 'nik');
    }

    /**
     * Accessor yang dipanggil saat kita mengakses '$pasien->checkups'.
     * Ini akan secara cerdas menggabungkan riwayat checkup.
     */
    public function getCheckupsAttribute(): Collection
    {
        // Jika user ini adalah karyawan (punya NIP), ambil riwayat berdasarkan NIP.
        if ($this->nip) {
            return $this->checkupsByNip;
        }
        // Jika tidak, pasti non-karyawan, ambil riwayat berdasarkan NIK.
        return $this->checkupsByNik;
    }

    // --- AKHIR DARI PERBAIKAN ---

    /**
     * Relasi ke model LokasiKlinik.
     */
    public function lokasi()
    {
        return $this->belongsTo(LokasiKlinik::class, 'id_lokasi');
    }
}