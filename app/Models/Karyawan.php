<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan'; // Nama tabel
    protected $primaryKey = 'nip'; // Primary Key
    public $incrementing = false; // Karena NIP bukan auto-increment
    protected $keyType = 'string'; // Tipe data NIP adalah string

    protected $fillable = [
        'nip', 'nama_karyawan', 'jabatan', 'kantor', 'email', 'alamat', 'agama', 'tanggal_lahir'
    ];
}
