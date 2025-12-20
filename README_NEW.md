## Tentang Aplikasi Klinik GKN

**Klinik GKN** adalah sistem informasi manajemen klinik dan pengadaan barang medis yang terintegrasi berbasis **Laravel**. Aplikasi ini dirancang untuk memudahkan operasional klinik multi-lokasi dengan fitur manajemen pasien, rekam medis digital, dan sistem pengadaan barang medis yang terstruktur.

### Fitur Utama:
- 📋 **Manajemen Pasien**: Registrasi, login, dan kartu pasien digital
- 🏥 **Rekam Medis Digital**: Pencatatan kunjungan, diagnosis, dan resep obat
- 💊 **Manajemen Obat & Alat Medis**: Stok barang, distribusi antar lokasi, tracking riwayat
- 📦 **Sistem Pengadaan**: Permintaan barang, approval, dan tracking barang masuk
- 📊 **Dashboard & Laporan**: Statistik kunjungan, pemakaian obat, dan analisis penyakit
- ⭐ **Feedback Pasien**: Sistem penilaian kepuasan pasien secara realtime
- 🔐 **Multi-Role & Permission**: Sistem kontrol akses berbasis role yang fleksibel

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.0+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)

---

## Sistem Role & Hak Akses

Aplikasi Klinik GKN menggunakan sistem role-based access control (RBAC) yang fleksibel. Terdapat **3 role utama** yang dapat disesuaikan dengan kebutuhan klien:

### 1. **PASIEN**
Pengguna dari kalangan karyawan atau non-karyawan klinik yang dapat:
- ✅ Registrasi dan login dengan akun pribadi
- ✅ **Melihat kartu pasien digital** *(hanya untuk karyawan dengan NIP)*
  - Informasi diri dan riwayat medis
  - Riwayat kunjungan dan hasil pemeriksaan
  - Hasil diagnosa dan resep obat
- ✅ Update informasi kantor/lokasi (untuk karyawan)
- ❌ Non-karyawan tidak memiliki akses ke kartu pasien digital (data hanya tersimpan di sistem dokter)
- ❌ Tidak dapat mengakses fungsi dokter atau pengadaan

**URL Login Pasien**: `http://127.0.0.1:8000/login`

### 2. **DOKTER**
Tenaga medis profesional yang dapat:
- ✅ Melihat daftar pasien di lokasi kliniknya
- ✅ Membuat dan mengelola rekam medis pasien (diagnosis, anamnesa, terapi)
- ✅ Meresepkan obat dan melihat stok obat tersedia
- ✅ Membuat permintaan (request) barang medis ke bagian pengadaan
- ✅ Melihat riwayat barang dan distribusi obat antar lokasi
- ✅ Mengakses laporan harian, pemakaian obat, data penyakit, dan kunjungan
- ✅ Menerima notifikasi feedback dari pasien

**URL Login Dokter**: `http://127.0.0.1:8000/admin/login`

### 3. **PENGADAAN**
Tim pengadaan/procurement yang dapat:
- ✅ Melihat dan memproses permintaan barang dari dokter
- ✅ Menginput barang masuk (dari supplier/pembelian) dengan tracking riwayat
- ✅ Mengelola stok barang di berbagai lokasi klinik
- ✅ Membuat dan mencetak surat distribusi barang antar lokasi
- ✅ Mengelola master data (kantor, satuan, kemasan, validasi WhatsApp)
- ✅ Melihat laporan feedback pasien dan analisis kepuasan
- ✅ Tracking lengkap distribusi barang (audit trail)

**URL Login Pengadaan**: `http://127.0.0.1:8000/admin/login`

### Catatan Penting:
- **Hak akses dapat dikustomisasi** sesuai kebutuhan klien tanpa perubahan kode aplikasi
- **Multi-lokasi**: Setiap dokter dan pengadaan dapat mengelola data lokasinya masing-masing
- **Sistem berbasis role fleksibel**: Sistem ini menggunakan tabel role-user yang dapat diperluas untuk role tambahan

---

## Kredensial Awal (Hasil Database Seeding)

<table border="1" cellpadding="8" cellspacing="0">
  <thead>
    <tr>
      <th>NIP/NIK</th>
      <th>Email</th>
      <th>Password</th>
      <th>Role</th>
      <th>URL Login</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>111111111111111111</td>
      <td>admin@example.com</td>
      <td>12345678</td>
      <td>DOKTER</td>
      <td>http://127.0.0.1:8000/admin/login</td>
    </tr>
    <tr>
      <td>222222222222222222</td>
      <td>admin2@example.com</td>
      <td>12345678</td>
      <td>DOKTER</td>
      <td>http://127.0.0.1:8000/admin/login</td>
    </tr>
    <tr>
      <td>333333333333333333</td>
      <td>admin3@example.com</td>
      <td>12345678</td>
      <td>PENGADAAN</td>
      <td>http://127.0.0.1:8000/admin/login</td>
    </tr>
  </tbody>
</table>

---

### **Dashboard**
Setiap role memiliki dashboard yang disesuaikan dengan kebutuhan mereka:

![Dashboard Comparison Placeholder](./docs/images/dashboard-comparison.png)
*Placeholder: Perbandingan dashboard antara pasien, dokter, dan pengadaan*

#### Dashboard Pasien
- Total kunjungan ke klinik
- Total pemeriksaan (check-up)
- Informasi kunjungan terakhir
- Pemeriksaan terakhir

#### Dashboard Dokter
- Jumlah kunjungan bulan ini (filtered by lokasi dokter)
- Daftar pasien dan riwayat medis
- Stok obat dan alat medis yang tersedia
- Permintaan barang yang menunggu approval
- Laporan penyakit dan pemakaian obat
- Feedback kepuasan pasien

#### Dashboard Pengadaan
- Total kunjungan bulanan (untuk context)
- Status permintaan barang (Pending, Approved, Completed, Rejected)
- Stok barang yang menipis
- Permintaan terbaru yang masih pending
- Trend barang paling diminta
- Distribusi barang per lokasi
- Statistik feedback pasien

---

### **Manajemen Pasien** (Role: DOKTER)

#### 1. **Daftar Pasien**
- Melihat daftar semua pasien (karyawan dan non-karyawan)
- Pencarian berdasarkan nama, NIP, atau NIK
- Melihat detail informasi pasien lengkap
- Melihat riwayat medis pasien

#### 2. **Registrasi Pasien Non-Karyawan**
- Dokter dapat mendaftarkan pasien non-karyawan (pengunjung/pasien umum)
- Input data pribadi, alamat, dan kontak
- Sistem otomatis generate NIK jika belum ada

#### 3. **Kartu Pasien Digital** (Role: PASIEN - Hanya Karyawan)

**⚠️ Fitur ini HANYA tersedia untuk karyawan dengan NIP - Non-karyawan TIDAK memiliki akses**

Karyawan yang telah login dapat melihat kartu pasien digital mereka melalui menu "Kartu Pasien Saya":
- Informasi pribadi (nama, NIP, kantor, lokasi)
- Riwayat kunjungan dan pemeriksaan lengkap
- Hasil diagnosa dan resep obat
- Update informasi kantor jika lokasi kerja berubah

**Untuk Non-Karyawan:**
- ❌ **TIDAK memiliki akses ke kartu pasien digital**
- Jika mencoba mengakses halaman `/kartu-pasien`, akan di-redirect ke dashboard dengan pesan:
  > "Kartu pasien digital hanya tersedia untuk karyawan. Data Anda telah tercatat di sistem dokter."
- Data medis mereka hanya tersimpan di sistem dokter
- Dapat melihat/cetak hasil pemeriksaan langsung dari dokter atau melalui surat resmi saat konsultasi

**Implementasi:**
- Validasi dilakukan di `PasienController::myCard()` - mengecek keberadaan relasi `karyawan` dan kolom `nip`
- Navigation menu hanya menampilkan link "Kartu Pasien Saya" untuk user dengan `$user->karyawan` relation
- Sistem ini memastikan privacy dan compliance - non-karyawan hanya mendapat akses yang sesuai kebutuhan mereka

![Kartu Pasien Digital Placeholder](./docs/images/patient-card.png)
*Placeholder: Contoh tampilan kartu pasien digital untuk karyawan*

---

### **Rekam Medis & Check-up** (Role: DOKTER)

![Rekam Medis Interface Placeholder](./docs/images/medical-record.png)
*Placeholder: Interface pembuatan rekam medis dan input diagnosis*

#### 1. **Membuat Rekam Medis**
Dokter dapat mencatat kunjungan pasien dengan detail:
- **Tanggal Kunjungan**: Tanggal pemeriksaan
- **Anamnesa**: Keluhan/riwayat dari pasien
- **Diagnosa**: Diagnosis penyakit (menggunakan kode ICD-10)
  - Support pencarian otomatis penyakit berdasarkan kode atau nama
  - Multiple diagnosis untuk satu kunjungan
- **Terapi/Treatment**: Tindakan atau terapi yang diberikan
- **Resep Obat**: Memberikan resep obat dengan jumlah dan kemasan
  - Sistem otomatis cek stok obat yang tersedia di lokasi klinik
  - Pencarian obat otomatis berdasarkan nama atau kode
  - Stok obat langsung berkurang saat resep disimpan

#### 2. **Check-up / Pemeriksaan**
Dokter dapat mencatat hasil pemeriksaan vital pasien:
- Tanggal pemeriksaan
- Tekanan darah
- Gula darah
- Kolesterol
- Catatan tambahan

#### 3. **Print Resep Obat**
- Resep dapat dicetak dalam format thermal printer 80mm
- Cocok untuk resep di apotek klinik
- Berisi nama pasien, dokter, obat, dan tanda tangan dokter

![Resep Obat Template Placeholder](./docs/images/prescription-print.png)
*Placeholder: Template resep obat yang siap dicetak*

---

### **Pengadaan Barang Medis** (Role: DOKTER & PENGADAAN)

![Pengadaan Workflow Placeholder](./docs/images/procurement-workflow.png)
*Placeholder: Alur pengadaan dari permintaan hingga distribusi barang*

#### 1. **Permintaan Barang** (Role: DOKTER)
Dokter dapat membuat permintaan barang medis kepada bagian pengadaan:
- **Barang Terdaftar**: Memilih barang yang sudah ada di sistem
- **Barang Baru**: Menambahkan barang baru yang belum terdaftar
- **Tracking Status**:
  - `PENDING`: Menunggu disetujui pengadaan
  - `APPROVED`: Sudah disetujui
  - `PROCESSING`: Sedang diproses pengadaan
  - `COMPLETED`: Barang sudah masuk dan siap digunakan
  - `REJECTED`: Permintaan ditolak

#### 2. **Proses Permintaan** (Role: PENGADAAN)
Pengadaan dapat memproses permintaan dari dokter:
- Melihat daftar permintaan dengan status
- Lihat detail barang yang diminta
- Approve atau reject permintaan
- Generate purchase order (PO)
- Print PDF permintaan untuk referensi procurement

#### 3. **Barang Masuk**
Pengadaan mencatat barang yang telah diterima dari supplier:
- Input barang masuk langsung (tanpa permintaan)
- Input barang masuk dari permintaan yang sudah disetujui
- **Multi-input**: Support input banyak barang sekaligus
- **Riwayat Barang Masuk**: Tracking semua barang yang pernah masuk dengan detail:
  - Tanggal transaksi
  - Nama barang dan kode obat
  - Jumlah dan kemasan
  - Lokasi penerima
  - User yang input

#### 4. **Stok Barang & Distribusi**
Pengadaan dapat mengelola stok barang di berbagai lokasi:
- **Daftar Barang Medis**: Melihat master data semua obat dan alat medis
- **Stok per Lokasi**: Stok barang di setiap lokasi klinik
- **Riwayat Stok**: Tracking lengkap setiap perubahan stok (masuk, keluar, distribusi)
- **Distribusi Barang**: Mengirim barang dari satu lokasi ke lokasi lain
  - Single item distribution
  - Multi-item distribution
  - Automatic stok reduction dan increment
- **Surat Distribusi**: Generate dan print surat resmi distribusi barang
  - Lihat detail distribusi barang
  - Print PDF untuk dokumentasi
  - Update status WhatsApp notifikasi

#### 5. **Master Data** (Role: PENGADAAN)
Pengadaan dapat mengelola data master yang mendukung operasional:

**Master Kantor**
- Daftar kantor/lokasi karyawan yang dilayani klinik
- Informasi kantor (nama, alamat, contact person)
- Status aktif/nonaktif

**Master Satuan**
- Tabel satuan barang medis (box, strip, tablet, ml, pcs, dll)
- Dapat dikustomisasi sesuai kebutuhan

**Master Isi Kemasan**
- Informasi jumlah satuan terkecil dalam satu kemasan
- Misal: 1 box = 10 strip, 1 strip = 10 tablet

**Master WhatsApp Validator**
- Daftar nomor WhatsApp untuk notifikasi
- Validasi format nomor WhatsApp
- Status aktif/nonaktif

---

### **Laporan & Analisis** (Role: DOKTER)

![Laporan Analytics Placeholder](./docs/images/reports-analytics.png)
*Placeholder: Dashboard laporan dengan grafik dan statistik penyakit*

#### 1. **Laporan Harian**
- Daftar kunjungan hari ini
- Total kunjungan per hari
- Informasi pasien dan diagnosa

#### 2. **Laporan Pemakaian Obat**
- Total penggunaan obat dalam periode tertentu
- Obat yang paling sering digunakan
- Trend penggunaan obat

#### 3. **Laporan Penyakit**
- Daftar penyakit yang paling banyak ditemukan
- Statistik penyakit per bulan/tahun
- Menggunakan kode ICD-10

#### 4. **Laporan Kunjungan**
- Total kunjungan pasien
- Statistik kunjungan per period
- Analisis trend kunjungan

#### 5. **Export Laporan**
- Export ke format PDF
- Siap untuk presentasi atau dokumentasi

---

### **Feedback Pasien** (Role: PENGADAAN)

![Feedback Form Placeholder](./docs/images/feedback-tablet.png)
*Placeholder: Interface tablet untuk feedback pasien dengan rating emoji*

#### Fitur Feedback:
- **Tablet Perawat**: Pasien memberikan feedback setelah konsultasi (bisa tanpa login)
- **Rating Kepuasan**: Skala 1-5 dengan emoji representation
- **Komentar Verbal**: Tempat pasien menulis komentar/saran
- **Auto-Popup**: Feedback form otomatis muncul setelah konsultasi
- **Laporan Pengadaan**: Pengadaan dapat melihat laporan feedback untuk quality assurance

---

### **Daftar Penyakit** (Role: DOKTER)

#### Fitur:
- **Master ICD-10**: Daftar penyakit standar dengan kode ICD-10
- **CRUD Penyakit**: Tambah, edit, hapus penyakit
- **Pencarian**: Cari penyakit berdasarkan kode ICD-10 atau nama penyakit
- **Auto-complete**: Saat input diagnosa di rekam medis

---

## Flow Alur Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│                        ALUR SISTEM KLINIK GKN                    │
└─────────────────────────────────────────────────────────────────┘

1. REGISTRASI & LOGIN PASIEN
   ├─ Pasien registrasi di halaman /register
   ├─ Input NIP (karyawan) atau NIK (non-karyawan)
   ├─ Verifikasi email
   └─ Login ke dashboard pasien

2. KUNJUNGAN KE KLINIK
   ├─ Pasien hadir ke klinik
   ├─ Dokter membuat rekam medis
   │  ├─ Input anamnesa (keluhan)
   │  ├─ Pilih diagnosis (ICD-10)
   │  ├─ Input terapi/treatment
   │  └─ Resepkan obat
   ├─ Stok obat otomatis berkurang
   └─ Feedback form popup untuk pasien

3. PERMINTAAN BARANG
   ├─ Dokter membuat permintaan barang ke pengadaan
   ├─ Pengadaan review dan approve/reject
   └─ Status berubah: PENDING → APPROVED → COMPLETED

4. PENGADAAN BARANG
   ├─ Supplier mengirim barang
   ├─ Pengadaan input barang masuk
   ├─ Stok barang bertambah
   └─ Generate surat distribusi (jika perlu dipindah lokasi)

5. DISTRIBUSI BARANG
   ├─ Pengadaan kirim barang ke lokasi lain
   ├─ Stok di lokasi asal berkurang
   ├─ Stok di lokasi tujuan bertambah
   └─ Print surat distribusi resmi

6. LAPORAN & ANALISIS
   ├─ Dokter lihat laporan penyakit, obat, kunjungan
   ├─ Pengadaan lihat laporan stok dan feedback pasien
   └─ Manage/export laporan ke PDF
```

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
DB_DATABASE=klinik_db
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
