<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class NonKaryawan extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'non_karyawan';

    /**
     * [DIUBAH] Mendefinisikan NIK sebagai Primary Key.
     */
    protected $primaryKey = 'nik';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Atribut yang dapat diisi secara massal.
     * Kolom 'nama' dihilangkan karena sekarang berada di tabel 'users'.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nik',
        'alamat',
        'lokasi_gedung',
        'tanggal_lahir',
    ];

    /**
     * [BARU] Relasi ke model User.
     * Satu profil NonKaryawan dimiliki oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }

    /**
     * Accessor untuk menghitung umur secara otomatis.
     *
     * @return int
     */
    public function getUmurAttribute()
    {
        // Pengecekan untuk memastikan tanggal lahir tidak null sebelum di-parse
        if (isset($this->attributes['tanggal_lahir'])) {
            return Carbon::parse($this->attributes['tanggal_lahir'])->age;
        }
        return 0;
    }
}