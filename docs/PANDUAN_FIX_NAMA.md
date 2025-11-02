# 📋 PANDUAN: Tampilkan Nama Lengkap (Bukan NIP)

## 🎯 Tujuan
Mengubah tampilan dari:
```
Pasien: NIP: 198702142010333332  ❌
Dokter: NIP: 11111111111111111   ❌
```

Menjadi:
```
Pasien: John Doe          ✅
Dokter: Dr. Jane Smith    ✅
```

---

## ⚠️ INFORMASI PENTING

**Kolom nama di database:**
- Tabel `users`: **`nama_karyawan`** (BUKAN `name`)
- Tabel `karyawan`: **`nama_karyawan`** (BUKAN `nama`)

Kedua tabel menggunakan nama kolom yang **sama**.

---

## 🚀 Cara Tercepat (Pilih Salah Satu)

### **OPSI 1: Via Artisan Command** ⚡ (Paling Mudah)

```bash
php artisan karyawan:sync-names
```

**Kelebihan:**
- ✅ Otomatis
- ✅ Ada validasi
- ✅ Menampilkan detail perubahan
- ✅ Bisa dry-run dulu: `php artisan karyawan:sync-names --dry-run`

---

### **OPSI 2: Via phpMyAdmin** 📊

#### Langkah 1: Buka phpMyAdmin
Akses database `klinik_db` Anda.

#### Langkah 2: Jalankan Query SQL

Copy-paste dan jalankan query ini **satu per satu**:

##### A. Update Karyawan yang Nama Kosong

```sql
UPDATE karyawan k
INNER JOIN users u ON k.nip = u.nip
SET k.nama_karyawan = u.nama_karyawan
WHERE (k.nama_karyawan IS NULL OR k.nama_karyawan = '' OR TRIM(k.nama_karyawan) = '')
AND u.nama_karyawan IS NOT NULL
AND TRIM(u.nama_karyawan) != '';
```

**Output:** `X rows affected` (X = jumlah data yang di-update)

##### B. Insert Users yang Belum Ada di Karyawan

```sql
INSERT INTO karyawan (nip, nama_karyawan, alamat, no_hp, id_lokasi, created_at, updated_at)
SELECT 
    u.nip,
    u.nama_karyawan,
    '-' as alamat,
    '-' as no_hp,
    1 as id_lokasi,
    NOW() as created_at,
    NOW() as updated_at
FROM users u
WHERE u.nip IS NOT NULL
AND u.nip NOT IN (SELECT nip FROM karyawan WHERE nip IS NOT NULL)
ON DUPLICATE KEY UPDATE 
    nama_karyawan = IF(karyawan.nama_karyawan IS NULL OR karyawan.nama_karyawan = '', VALUES(nama_karyawan), karyawan.nama_karyawan),
    updated_at = NOW();
```

**Output:** `Y rows affected` (Y = jumlah data yang di-insert)

#### Langkah 3: Refresh Browser

```
Tekan: Ctrl + Shift + R
```

Lalu akses halaman print resep lagi. **Nama lengkap seharusnya sudah muncul!**

---

### **OPSI 3: Via Tinker** 🔧

```bash
php artisan tinker
```

Lalu paste code ini:

```php
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

// Update karyawan dengan nama kosong
$updated = DB::table('karyawan as k')
    ->join('users as u', 'k.nip', '=', 'u.nip')
    ->whereRaw('(k.nama_karyawan IS NULL OR k.nama_karyawan = "")')
    ->update(['k.nama_karyawan' => DB::raw('u.nama_karyawan')]);

echo "✅ Updated {$updated} records\n";

// Insert users yang belum ada di karyawan
$users = User::whereNotNull('nip')
    ->whereNotIn('nip', function($q) {
        $q->select('nip')->from('karyawan')->whereNotNull('nip');
    })
    ->get();

foreach ($users as $user) {
    Karyawan::create([
        'nip' => $user->nip,
        'nama_karyawan' => $user->nama_karyawan,
        'alamat' => '-',
        'no_hp' => '-',
        'id_lokasi' => 1,
    ]);
}

echo "✅ Inserted {$users->count()} records\n";
exit;
```

---

## ✅ Verifikasi Hasil

Jalankan query ini untuk cek hasilnya:

```sql
SELECT 
    u.id,
    u.nip,
    u.nama_karyawan as nama_user,
    k.nama_karyawan as nama_karyawan,
    CASE 
        WHEN k.nama_karyawan IS NOT NULL AND k.nama_karyawan != '' THEN '✅ OK'
        ELSE '❌ MASIH KOSONG'
    END as status
FROM users u
LEFT JOIN karyawan k ON u.nip = k.nip
WHERE u.nip IS NOT NULL
ORDER BY status, u.id;
```

**Expected:** Semua status harus **✅ OK**

---

## 🔧 Manual Fix (Jika Masih Ada yang Kosong)

Jika setelah sync masih ada nama yang kosong, bisa update manual:

```sql
-- Pasien dengan NIP 198702142010333332
UPDATE karyawan 
SET nama_karyawan = 'John Doe' 
WHERE nip = '198702142010333332';

-- Dokter dengan NIP 11111111111111111
UPDATE karyawan 
SET nama_karyawan = 'Dr. Jane Smith' 
WHERE nip = '11111111111111111';

-- Atau update via users.id
UPDATE karyawan k
INNER JOIN users u ON k.nip = u.nip
SET k.nama_karyawan = 'Dr. Jane Smith'
WHERE u.id = 1;
```

---

## 🐛 Troubleshooting

### Problem: Masih Tampil NIP

**Cek 1:** Verifikasi data di database
```sql
SELECT u.nip, u.nama_karyawan as user_name, k.nama_karyawan as karyawan_name
FROM users u
LEFT JOIN karyawan k ON u.nip = k.nip
WHERE u.nip = '198702142010333332';
```

**Cek 2:** Hard refresh browser
```
Tekan: Ctrl + Shift + R
Atau: Ctrl + F5
```

**Cek 3:** Clear cache Laravel
```bash
php artisan cache:clear
php artisan view:clear
```

### Problem: Query Error

**Error:** `Unknown column 'u.name'`
- ✅ **Solusi:** Ganti `u.name` dengan `u.nama_karyawan`

**Error:** `Unknown column 'k.nama'`
- ✅ **Solusi:** Ganti `k.nama` dengan `k.nama_karyawan`

---

## 📝 Catatan Teknis

### Struktur Tabel

**Tabel `users`:**
```sql
- id (primary key)
- nip (unique, nullable)
- nik (unique, nullable)  
- nama_karyawan ← NAMA DISINI
- email (unique, nullable)
- akses (enum: DOKTER, PENGADAAN, PASIEN)
```

**Tabel `karyawan`:**
```sql
- id (primary key)
- nip (unique)
- nama_karyawan ← NAMA DISINI
- alamat
- no_hp
- id_lokasi
```

### Logika Fallback di View

File: `resources/views/rekam-medis/print-resep.blade.php`

```php
// Priority 1: Nama dari karyawan
$namaPasien = $rekamMedis->user->karyawan->nama_karyawan ?? null;

// Priority 2: Nama dari user
if (!$namaPasien) {
    $namaPasien = $rekamMedis->user->nama_karyawan ?? null;
}

// Priority 3: Tampilkan NIP jika nama kosong
if (!$namaPasien) {
    $namaPasien = 'NIP: ' . $rekamMedis->user->nip;
}
```

Fallback ini **tetap jalan** meski database belum di-sync, tapi akan prioritas tampilkan nama jika ada.

---

## ✅ Hasil Akhir

Setelah sync, saat print resep akan tampil:

```
═══════════════════════════════
      KLINIK GIGI KEMENKUMHAM
═══════════════════════════════

Pasien: John Doe
Dokter: Dr. Jane Smith
Tanggal: 09/10/2025

-----------------------------------
RESEP OBAT
-----------------------------------
1. Paracetamol 500mg
   Dosis: 3x1 tablet sehari
   
2. Amoxicillin 500mg
   Dosis: 2x1 kapsul sehari
-----------------------------------
```

**Tidak ada NIP lagi yang tampil!** 🎉
