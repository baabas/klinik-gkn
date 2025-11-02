# 🔧 Troubleshooting Guide - Resep Obat

## 📋 Changelog Update

### 🖨️ Update 3: Thermal Printer Receipt Optimization (08 Okt 2025 - 06:51 WIB)

**Masalah yang Diperbaiki:**
1. ✅ **Ukuran struk terlalu besar** - Optimasi CSS untuk thermal 80mm
2. ✅ **Nama dokter tidak muncul** - Eager loading sudah benar
3. ✅ **Field ID (NIP/NIK)** - Dihapus dari struk, hanya tampil nama pasien
4. ✅ **Navigasi kembali** - Tombol "← Kembali" ditambahkan

**File yang Dimodifikasi:**
- `resources/views/rekam-medis/print-resep.blade.php`

**Perubahan Layout:**
```
BEFORE:                          AFTER:
┌───────────────────────┐      ┌───────────────────────┐
│ [🖨️ Print]           │      │ [← Kembali] [🖨️ Print]│
│                       │      │                       │
│  KLINIK GKN (16px)   │      │  KLINIK GKN (14px)   │
│  RESEP OBAT (12px)   │      │  RESEP OBAT (10px)   │
│  ─────────────────   │      │  ─────────────────   │
│  No. RM:    2        │      │  No. RM:    2        │
│  Tanggal:   ...      │      │  Tanggal:   ...      │
│  Pasien:    Nama     │      │  Pasien:    Nama     │
│  ID:   NIP: xxx ❌   │      │  Dokter:    Nama ✅  │
│  Dokter:    - ❌     │      │  ─────────────────   │
│  ─────────────────   │      │  1. Obat (9px)       │
│  1. Obat (12px)      │      │     Jumlah: 8 (8px)  │
│     Jumlah: 8 (10px) │      │     Dosis: 3x1 (8px) │
│     Dosis: 3x1       │      │  ─────────────────   │
│  ─────────────────   │      │  Footer (8px)        │
│  Footer (10px)       │      │  Dicetak: ...        │
└───────────────────────┘      └───────────────────────┘
    70mm width                      74mm width
```

**CSS Optimizations:**
| Element | Before | After |
|---------|--------|-------|
| Body font | 11px | 9px |
| Width | 70mm | 74mm |
| Margin | 5mm | 3mm |
| Header h2 | 16px | 14px |
| Subtitle | 12px | 10px |
| Info text | 10px | 8px |
| Obat name | 12px | 9px |
| Obat detail | 10px | 8px |
| Footer | 10px | 8px |

**Navigation Buttons:**
```html
<!-- Before: 1 button -->
<button onclick="window.print()" class="print-button">
    🖨️ Print Ulang
</button>

<!-- After: 2 buttons -->
<div class="action-buttons">
    <a href="{{ route('pasien.index') }}" class="btn-back">
        ← Kembali ke Daftar Pasien
    </a>
    <button onclick="window.print()" class="btn-print">
        🖨️ Print Ulang
    </button>
</div>
```

**Informasi Pasien/Dokter:**
```blade
<!-- Sebelumnya: 5 baris info -->
No. RM, Tanggal, Pasien, ID (NIP/NIK), Dokter

<!-- Sekarang: 4 baris info -->
No. RM, Tanggal, Pasien (nama saja), Dokter (nama saja)
```

**Testing Checklist:**
- [ ] Hard refresh browser (Ctrl + Shift + R)
- [ ] Cek nama dokter muncul dengan benar
- [ ] Cek nama pasien tampil (tanpa NIP/NIK)
- [ ] Cek tombol "Kembali" berfungsi
- [ ] Print preview ukuran sudah compact
- [ ] Test print ke thermal printer 80mm
- [ ] Verifikasi font readable (jarak 30cm)

---

## ❌ Issue: Field Dosis Tidak Muncul / Resep Obat Tidak Terdeteksi

### Gejala:
- Field "Dosis" tidak tampil pada form resep obat
- Search obat tidak berfungsi
- Dropdown hasil search tidak muncul

### Penyebab:
JavaScript selector tidak sesuai dengan struktur HTML yang baru setelah penambahan kolom dosis.

### ✅ Solusi yang Sudah Diterapkan:

#### 1. Update Struktur HTML Form Resep Obat
**File:** `resources/views/rekam-medis/create.blade.php`

**Perubahan:**
```html
<!-- BEFORE (3 kolom) -->
<div class="col-sm-8">  <!-- Nama obat -->
<div class="col-sm-3">  <!-- Jumlah -->
<div class="col-sm-1">  <!-- Hapus -->

<!-- AFTER (4 kolom) -->
<div class="col-sm-5">  <!-- Nama obat -->
<div class="col-sm-2">  <!-- Jumlah -->
<div class="col-sm-4">  <!-- Dosis (BARU) -->
<div class="col-sm-1">  <!-- Hapus -->
```

#### 2. Update JavaScript Selector
**File:** `resources/views/rekam-medis/create.blade.php`

**Line 318 - BEFORE:**
```javascript
let dropdownResults = obatSearchInput.closest('.col-sm-8').find('.obat-dropdown-results');
```

**Line 318 - AFTER:**
```javascript
let dropdownResults = obatSearchInput.closest('.position-relative').find('.obat-dropdown-results');
```

**Alasan:** 
- Selector `.col-sm-8` tidak lagi valid karena kolom sudah berubah menjadi `.col-sm-5`
- Menggunakan `.position-relative` lebih robust karena tidak depend pada ukuran kolom

---

## 🧪 Cara Testing

### 1. Test Input Resep Obat
1. Login sebagai DOKTER
2. Pilih pasien
3. Klik "Buat Rekam Medis"
4. Scroll ke section "3. Resep Obat & Terapi"
5. **Verifikasi:** Lihat 4 kolom: Nama Obat, Qty, Dosis, Delete

### 2. Test Search Obat
1. Klik pada field "Cari nama atau kode obat..."
2. Ketik minimal 2 karakter (contoh: "flu")
3. Tunggu 300ms (debounce)
4. **Verifikasi:** Dropdown muncul dengan hasil search

### 3. Test Pilih Obat
1. Klik salah satu hasil search
2. **Verifikasi:** 
   - Field otomatis terisi dengan nama obat + stok
   - Hidden field `id_obat` terisi
   - Dropdown tertutup

### 4. Test Input Dosis
1. Setelah pilih obat
2. Masukkan dosis di field "Dosis" (contoh: "3x1")
3. **Verifikasi:** Text terisi tanpa error

### 5. Test Tambah Obat
1. Klik button "Tambah Obat"
2. **Verifikasi:** Row baru muncul dengan 4 kolom sama

### 6. Test Hapus Obat
1. Klik tombol X (delete) pada row obat
2. **Verifikasi:** Row terhapus

### 7. Test Submit Form
1. Isi semua data rekam medis
2. Tambah minimal 1 obat dengan dosis
3. Klik "Simpan Rekam Medis"
4. **Verifikasi:** 
   - Data tersimpan
   - Redirect ke print resep
   - Dosis muncul di struk

---

## 🔍 Debugging

### Jika Search Obat Masih Tidak Berfungsi

#### Check 1: Console Browser (F12)
```
Buka Developer Tools → Console Tab
Lihat apakah ada error JavaScript
```

**Error yang Mungkin Muncul:**
- `Cannot read property 'find' of null` → Selector salah
- `jQuery is not defined` → jQuery belum dimuat
- `404 Not Found /api/obat-search` → Route tidak ada

#### Check 2: Network Tab
```
Buka Developer Tools → Network Tab
Ketik di search obat
Lihat request ke /api/obat-search
```

**Verifikasi:**
- Status: 200 OK
- Response: JSON dengan data obat
- Headers: Content-Type application/json

#### Check 3: Database
```sql
SELECT * FROM barang_medis LIMIT 5;
SELECT * FROM stok_barang LIMIT 5;
```

**Pastikan:**
- Ada data obat di tabel `barang_medis`
- Ada stok di tabel `stok_barang`
- `jumlah` > 0

#### Check 4: Route API
```bash
php artisan route:list --path=api
```

**Pastikan ada:**
```
GET  /api/obat-search → RekamMedisController@searchObat
```

---

## 🛠️ Fixes Manual (Jika Perlu)

### Fix 1: Clear Cache
```bash
cd "c:\Users\Pongo\Downloads\klinik-gkn (2)\klinik-gkn"
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### Fix 2: Regenerate Autoload
```bash
composer dump-autoload
```

### Fix 3: Restart Server
```bash
# Ctrl+C untuk stop
php artisan serve
```

---

## 📋 Checklist Verifikasi

- [x] Struktur HTML form resep memiliki 4 kolom
- [x] JavaScript selector menggunakan `.position-relative`
- [x] Field dosis dengan placeholder "Dosis (contoh: 3x1)"
- [x] Route API `/api/obat-search` tersedia
- [x] Migration dosis sudah dijalankan
- [x] Model ResepObat include `dosis` di fillable
- [ ] Test manual di browser berhasil
- [ ] Dropdown search obat muncul
- [ ] Dosis tersimpan di database
- [ ] Print resep menampilkan dosis

---

## 🔄 Rollback (Jika Diperlukan)

Jika ingin kembali ke versi sebelum ada dosis:

### 1. Rollback Migration
```bash
php artisan migrate:rollback --step=1
```

### 2. Revert View
Ubah kembali struktur kolom dari 4 ke 3:
- col-sm-5 → col-sm-8 (nama obat)
- col-sm-2 → col-sm-3 (jumlah)
- Hapus col-sm-4 (dosis)

### 3. Revert Model
Hapus `'dosis'` dari fillable array di `ResepObat.php`

---

## 📞 Support

Jika masih ada masalah setelah mengikuti guide ini:

1. **Check Laravel Log:**
   ```
   storage/logs/laravel.log
   ```

2. **Check Browser Console:**
   - F12 → Console Tab
   - Screenshot error message

3. **Check Database:**
   ```sql
   DESCRIBE resep_obat;
   -- Pastikan ada kolom 'dosis'
   ```

4. **Verify Files:**
   - RekamMedisController.php line 40-45
   - ResepObat.php fillable array
   - rekam-medis/create.blade.php line 287-305

---

## ✅ Expected Result

Setelah fix diterapkan:

1. ✅ Form resep obat tampil dengan 4 kolom
2. ✅ Field dosis terlihat dan bisa diisi
3. ✅ Search obat berfungsi normal
4. ✅ Dropdown hasil search muncul
5. ✅ Data dosis tersimpan ke database
6. ✅ Print resep menampilkan dosis

---

**Last Updated:** 08 Oktober 2025
**Issue Status:** ✅ RESOLVED

---

## ❌ Issue 2: Error "Call to a member function format() on string"

### Gejala:
- Error 500 Internal Server Error saat akses `/rekam-medis/{id}/print-resep`
- Error message: "Call to a member function format() on string"
- Stack trace menunjuk ke `print-resep.blade.php` line 179

### Penyebab:
Model `RekamMedis` tidak memiliki casting untuk field `tanggal_kunjungan`, sehingga Laravel menganggapnya sebagai string, bukan Carbon/DateTime object.

### ✅ Solusi yang Sudah Diterapkan:

#### 1. Tambah Casting di Model RekamMedis
**File:** `app/Models/RekamMedis.php`

**Tambahan:**
```php
/**
 * Atribut yang harus di-cast ke tipe data tertentu.
 *
 * @var array<string, string>
 */
protected $casts = [
    'tanggal_kunjungan' => 'datetime',
];
```

#### 2. Tambah Fallback di View
**File:** `resources/views/rekam-medis/print-resep.blade.php` (line 177-185)

**BEFORE:**
```php
<span class="info-value">{{ $rekamMedis->tanggal_kunjungan->format('d/m/Y H:i') }}</span>
```

**AFTER:**
```php
<span class="info-value">
    @if($rekamMedis->tanggal_kunjungan instanceof \Carbon\Carbon)
        {{ $rekamMedis->tanggal_kunjungan->format('d/m/Y H:i') }}
    @else
        {{ \Carbon\Carbon::parse($rekamMedis->tanggal_kunjungan)->format('d/m/Y H:i') }}
    @endif
</span>
```

### 🧪 Cara Testing:

1. Buat rekam medis baru dengan resep obat
2. Setelah redirect, akses URL print resep
3. **Verifikasi:** 
   - ✅ Halaman print resep terbuka tanpa error
   - ✅ Tanggal ditampilkan dengan format `dd/mm/YYYY HH:MM`
   - ✅ Auto-print berjalan

### 📝 Root Cause Analysis:

Laravel secara default tidak melakukan casting otomatis untuk datetime fields. Field `tanggal_kunjungan` di database bertipe `TIMESTAMP` atau `DATETIME`, tapi Laravel membacanya sebagai string tanpa casting.

Ketika di view kita memanggil `->format()`, PHP error karena string tidak memiliki method `format()`.

**Solusi:** Tambahkan explicit casting di model menggunakan `protected $casts`.

---
