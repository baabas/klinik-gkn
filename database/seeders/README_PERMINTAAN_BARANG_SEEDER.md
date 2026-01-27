# Seeder Permintaan Barang (Pending & Trending)

## Deskripsi
Seeder ini membuat data permintaan barang untuk menguji fitur:
- **Notifikasi permintaan pending** (5 permintaan PENDING)
- **Trending barang bulan ini** (9 permintaan APPROVED/COMPLETED)

## Cara Menggunakan

### 1. Jalankan Semua Seeder (Fresh Install)
```bash
php artisan migrate:fresh --seed
```

### 2. Jalankan Hanya PermintaanBarangSeeder
```bash
php artisan db:seed --class=PermintaanBarangSeeder
```

**PENTING:** Seeder ini memerlukan data lain sudah dibuat:
- Lokasi Klinik (PengadaanSeeder)
- Barang Medis (BarangMedisSeeder)
- User Dokter (AdminUserSeeder)

## Data yang Dibuat

### Permintaan PENDING (5 permintaan)

1. **REQ-[TODAY]-001** - GKN 1
   - Status: PENDING
   - Tanggal: Hari ini
   - Item: Paracetamol (200), Amoxicillin (150)
   - Catatan: Permintaan rutin bulanan untuk stok kritis

2. **REQ-[YESTERDAY]-001** - GKN 2
   - Status: PENDING
   - Tanggal: Kemarin
   - Item: Vitamin C (100), Omeprazole (80)
   - Catatan: Permintaan untuk stok menipis

3. **REQ-[TODAY]-002** - GKN 1
   - Status: PENDING
   - Tanggal: Hari ini
   - Item: **Salbutamol Inhaler (20)** - BARANG BARU
   - Catatan: Permintaan obat baru yang belum terdaftar

4. **REQ-[3-DAYS-AGO]-001** - GKN 2
   - Status: PENDING
   - Tanggal: 3 hari lalu
   - Item: Ibuprofen/Antasida (150)
   - Catatan: Permintaan untuk persiapan bulan depan

5. **REQ-[7-DAYS-AGO]-001** - GKN 1
   - Status: PENDING
   - Tanggal: 7 hari lalu
   - Item: Antasida (120)
   - Catatan: Permintaan darurat - URGENT

### Trending Items Bulan Ini (9 permintaan)

Permintaan dengan status APPROVED/COMPLETED untuk trending chart:

**Trending #1: Amoxicillin** (600 total)
- 250 kaplet (15 hari lalu) - COMPLETED
- 200 kaplet (12 hari lalu) - COMPLETED
- 150 kaplet (8 hari lalu) - COMPLETED

**Trending #2: Vitamin C** (300 total)
- 180 tablet (14 hari lalu) - COMPLETED
- 120 tablet (6 hari lalu) - APPROVED

**Trending #3: Omeprazole** (250 total)
- 150 kapsul (18 hari lalu) - COMPLETED
- 100 kapsul (10 hari lalu) - COMPLETED

**Trending #4: Ibuprofen** (200 total)
- 120 tablet (16 hari lalu) - COMPLETED
- 80 tablet (4 hari lalu) - APPROVED

## Fitur yang Dapat Diuji

### 1. Notifikasi Permintaan Pending
- Login sebagai user PENGADAAN
- Lihat badge pada menu "Daftar Permintaan"
- Seharusnya menampilkan angka **5** (jumlah permintaan pending)

### 2. Dashboard Pengadaan - Permintaan Terbaru
- Buka dashboard pengadaan
- Lihat bagian "Permintaan Pending"
- Seharusnya menampilkan 5 permintaan terbaru dengan:
  - Kode permintaan
  - Lokasi peminta
  - Tanggal permintaan
  - Tombol "Proses"

### 3. Dashboard Pengadaan - Trending Barang
- Buka dashboard pengadaan
- Lihat bagian "Trending Barang"
- Seharusnya menampilkan top 5 barang yang paling banyak diminta bulan ini:
  1. Amoxicillin (600)
  2. Vitamin C (300)
  3. Omeprazole (250)
  4. Ibuprofen (200)

### 4. Permintaan dengan Barang Baru
- Buka daftar permintaan
- Cari permintaan REQ-[TODAY]-002
- Seharusnya ada item "Salbutamol Inhaler" yang belum terdaftar di master data
- Pengadaan perlu menambahkan ke master data terlebih dahulu sebelum approve

### 5. Distribusi Permintaan per Lokasi
- Dashboard menampilkan distribusi permintaan:
  - GKN 1: beberapa permintaan
  - GKN 2: beberapa permintaan

## Catatan Penting

- Seeder ini membuat permintaan dengan tanggal bulan ini (Carbon::now())
- Status permintaan: PENDING, APPROVED, COMPLETED
- Trending items dihitung dari permintaan bulan berjalan
- Permintaan PENDING akan muncul di notifikasi pengadaan
- Permintaan COMPLETED sudah selesai diproses
