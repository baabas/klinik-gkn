<?php

namespace App\Helpers;

use Carbon\Carbon;

class SapaanHelper
{
    /**
     * Generate sapaan berdasarkan umur dan jenis kelamin
     *
     * @param string|null $jenisKelamin 'L' untuk Laki-laki, 'P' untuk Perempuan
     * @param \Carbon\Carbon|string|null $tanggalLahir
     * @return string
     */
    public static function generateSapaan($jenisKelamin = null, $tanggalLahir = null)
    {
        // Default sapaan jika data tidak lengkap
        if (!$jenisKelamin || !$tanggalLahir) {
            return 'Teman';
        }

        // Convert string to Carbon instance jika diperlukan
        if (is_string($tanggalLahir)) {
            $tanggalLahir = Carbon::parse($tanggalLahir);
        }

        // Hitung umur
        $umur = $tanggalLahir->age;

        // Tentukan sapaan berdasarkan umur dan jenis kelamin
        if ($umur < 18) {
            // Anak-anak
            return $jenisKelamin === 'L' ? 'Adik' : 'Adik';
        } elseif ($umur >= 18 && $umur < 30) {
            // Remaja/Muda
            return $jenisKelamin === 'L' ? 'Kak' : 'Kak';
        } elseif ($umur >= 30 && $umur < 50) {
            // Dewasa
            return $jenisKelamin === 'L' ? 'Pak' : 'Ibu';
        } else {
            // Lansia
            return $jenisKelamin === 'L' ? 'Pak' : 'Ibu';
        }
    }

    /**
     * Variasi sapaan dengan format kalimat
     *
     * @param string $nama
     * @param string|null $jenisKelamin
     * @param \Carbon\Carbon|string|null $tanggalLahir
     * @return string
     */
    public static function sapaan($nama, $jenisKelamin = null, $tanggalLahir = null)
    {
        $sapaan = self::generateSapaan($jenisKelamin, $tanggalLahir);
        return "{$sapaan} {$nama}";
    }

    /**
     * Get umur dari tanggal lahir
     *
     * @param \Carbon\Carbon|string|null $tanggalLahir
     * @return int
     */
    public static function getUmur($tanggalLahir = null)
    {
        if (!$tanggalLahir) {
            return 0;
        }

        if (is_string($tanggalLahir)) {
            $tanggalLahir = Carbon::parse($tanggalLahir);
        }

        return $tanggalLahir->age;
    }
}
