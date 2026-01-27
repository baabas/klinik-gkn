# Seeder Barang Medis dengan Variasi Stok

## Deskripsi
Seeder ini membuat 10 data obat dengan variasi stok untuk menguji fitur stok minimal:
- **3 obat** dengan stok **KRITIS** (stok ≤ minimal)
- **3 obat** dengan stok **WARNING** (minimal < stok ≤ 1.5× minimal)
- **4 obat** dengan stok **AMAN** (stok > 1.5× minimal)

## Cara Menggunakan

### 1. Jalankan Semua Seeder (Fresh Install)
```bash
php artisan migrate:fresh --seed
```

### 2. Jalankan Hanya BarangMedisSeeder
```bash
php artisan db:seed --class=BarangMedisSeeder
```

## Data yang Dibuat

### Obat dengan Stok KRITIS (Merah)
1. **Paracetamol 500mg** - 120/200 tablet
2. **Amoxicillin 500mg** - 80/150 kaplet  
3. **Antasida Tablet** - 100/100 tablet

### Obat dengan Stok WARNING (Kuning)
4. **Vitamin C 500mg** - 250/200 tablet
5. **Omeprazole 20mg** - 210/150 kapsul
6. **Cetirizine 10mg** - 140/100 tablet

### Obat dengan Stok AMAN (Hijau)
7. **Ibuprofen 400mg** - 350/200 tablet
8. **Metformin 500mg** - 600/300 tablet
9. **Captopril 25mg** - 500/250 tablet
10. **Alprazolam 0.5mg** - 800/100 tablet

*Format: Nama Obat - Stok Actual/Stok Minimal (dalam satuan terkecil)*

## Fitur yang Dapat Diuji

Setelah menjalankan seeder ini, Anda dapat menguji:

1. **Badge Notifikasi Stok Kritis**
   - Login sebagai user PENGADAAN
   - Lihat badge merah di menu "Daftar Obat & Alat Medis"
   - Seharusnya menampilkan angka **3** (jumlah obat kritis)

2. **Dashboard Pengadaan**
   - Buka dashboard pengadaan
   - Lihat bagian "Stok Kritis"
   - Seharusnya menampilkan 5 obat terendah dengan:
     - Indikator warna (merah/kuning/hijau)
     - Stok dalam satuan terkecil (tablet/kaplet/kapsul)
     - Stok minimal untuk perbandingan

3. **Resep Dokter**
   - Login sebagai DOKTER
   - Buat resep dengan obat yang stoknya kritis
   - Seharusnya tetap bisa membuat resep (tidak diblokir)
   - Stok akan berkurang setelah resep dibuat

## Catatan Penting

- Seeder ini memerlukan **lokasi klinik** sudah dibuat (oleh PengadaanSeeder)
- Stok dibuat untuk lokasi klinik pertama (GKN 1)
- Semua stok dalam satuan terkecil (tablet, kaplet, kapsul)
- Struktur kemasan: Box → Strip → Satuan Terkecil
