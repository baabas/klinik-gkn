## Tentang Klinik GKN

Adalah aplikasi manajemen klinik dan pengadaan barang medis berbasis **Laravel**, yang digunakan untuk:  
- Mengelola data pasien, karyawan, dan non-karyawan  
- Mencatat rekam medis  
- Mengatur pengadaan & stok obat  
- Membuat resep & notifikasi realtime

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.0+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)

---

## Hak Akses  

Hak akses meliputi:
1. Dokter
2. Pengadaan
<img src="https://raw.githubusercontent.com/baabas/klinik-gkn/main/public/images/logindokter.png" width="100">

3. Pasien (hanya bisa melihat kartu pasien digital)
![Login Pasien](https://raw.githubusercontent.com/baabas/klinik-gkn/main/public/images/loginpasien.png)

---

**Kredensial Awal (hasil seeding)**
<table border="1" cellpadding="8" cellspacing="0">
  <thead>
    <tr>
      <th>NIP</th>
      <th>Email</th>
      <th>Password</th>
      <th>Akses</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>111111111111111111</td>
      <td>admin@example.com</td>
      <td>12345678</td>
      <td>DOKTER</td>
    </tr>
    <tr>
      <td>222222222222222222</td>
      <td>admin2@example.com</td>
      <td>12345678</td>
      <td>DOKTER</td>
    </tr>
    <tr>
      <td>333333333333333333</td>
      <td>admin3@example.com</td>
      <td>12345678</td>
      <td>PENGADAAN</td>
    </tr>
  </tbody>
</table>

## Sistem Meliputi

1. Dashboard  
2. Pasien  
3. Rekam Medis  
4. Pengadaan Barang  
   - Permintaan Barang  
   - Barang Masuk  
   - Stok History  
5. Resep Obat  
6. Master Data  
   - User  
   - Karyawan / Non-Karyawan  
   - Barang Medis  
   - ICD-10  

---

## Flow Sistem  

**Pendaftaran → Dokter → Pengadaan → Resep → Obat → Done**

---

## Instalasi  
1. Prasyarat
Pastikan seluruh perangkat lunak berikut terpasang:

- **PHP 8.2 atau lebih baru** beserta Composer 2.
- **Ekstensi PHP umum**: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON (tersedia di instalasi PHP standar).
- **Node.js 18 LTS atau 20 LTS** beserta npm (dibutuhkan oleh Vite).
- **Database**:
  Konfigurasi menggunakan MySQL/MariaDB
- **Git** (jika meng-clone langsung dari repository).

2. Clone Repo  
   ```bash
   git clone https://github.com/baabas/klinik-gkn.git
   cd klinik-gkn
   ```
3. Install dependensi PHP menggunakan Composer
```bash
composer install
```

4. Install dependensi JavaScript menggunakan NPM
```bash
npm install
```

5. Salin file .env.example menjadi .env
```bash
cp .env.example .env
```

6. Generate application key
```bash
php artisan key:generate
```

7. Konfigurasi database di file .env
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=klinik_gkn
DB_USERNAME=root
DB_PASSWORD=
```

8. Jalankan migrasi database dan seeder
```bash
php artisan migrate --seed
```

9. Kompilasi asset frontend
```bash
npm run dev
```

10. Jalankan aplikasi
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`
