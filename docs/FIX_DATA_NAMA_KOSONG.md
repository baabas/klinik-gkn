# 🔧 FIX DATA: Nama Pasien & Dokter Kosong

## 📊 Berdasarkan Debug Info:

```
id_rekam_medis: 3
nip_pasien: 198702142010333332
nik_pasien: (NULL)
id_dokter: 1

pasien_exists: YES
pasien_karyawan_exists: YES
pasien_karyawan_nama: (KOSONG!) ❌

dokter_exists: YES
dokter_karyawan_exists: NO ❌
```

---

## ✅ Solution Applied

### Multi-Level Fallback Logic:

**Pasien:**
1. ✅ `karyawan.nama` (jika tidak kosong)
2. ✅ `users.name` (fallback)
3. ✅ `non_karyawan.nama` (untuk pasien umum)
4. ✅ `NIP: xxx` (jika semua kosong)
5. ✅ `NIK: xxx` (sebagai last resort)

**Dokter:**
1. ✅ `karyawan.nama` (jika tidak kosong)
2. ✅ `users.name` (fallback)
3. ✅ `NIP: xxx` (jika name kosong)
4. ✅ `User ID: xxx` (last resort)

---

## 🔧 Permanent Fix: Update Data di Database

### Query 1: Cek Data yang Bermasalah

```sql
-- Cek pasien dengan karyawan.nama kosong
SELECT 
    k.nip,
    k.nama as nama_karyawan,
    u.name as nama_user,
    u.email
FROM karyawan k
LEFT JOIN users u ON k.nip = u.nip
WHERE k.nama IS NULL OR k.nama = ''
ORDER BY k.nip;

-- Cek dokter tanpa data karyawan
SELECT 
    u.id,
    u.nip,
    u.name,
    u.email,
    r.nama_role
FROM users u
JOIN roles r ON u.id_role = r.id_role
WHERE r.nama_role = 'DOKTER'
AND u.nip NOT IN (SELECT nip FROM karyawan WHERE nip IS NOT NULL)
ORDER BY u.id;
```

---

### Query 2: Fix Pasien dengan Karyawan.nama Kosong

```sql
-- Update karyawan.nama dari users.name jika kosong
UPDATE karyawan k
JOIN users u ON k.nip = u.nip
SET k.nama = u.name
WHERE (k.nama IS NULL OR k.nama = '')
AND u.name IS NOT NULL
AND u.name != '';

-- Atau manual untuk pasien spesifik
UPDATE karyawan 
SET nama = 'Nama Pasien yang Benar' 
WHERE nip = '198702142010333332';
```

---

### Query 3: Fix Dokter Tanpa Data Karyawan

```sql
-- Insert data karyawan untuk dokter yang belum ada
INSERT INTO karyawan (nip, nama, alamat, no_hp, id_lokasi)
SELECT 
    u.nip,
    u.name as nama,
    '-' as alamat,
    '-' as no_hp,
    1 as id_lokasi
FROM users u
JOIN roles r ON u.id_role = r.id_role
WHERE r.nama_role = 'DOKTER'
AND u.nip IS NOT NULL
AND u.nip NOT IN (SELECT nip FROM karyawan WHERE nip IS NOT NULL);

-- Atau manual untuk dokter spesifik (id_dokter = 1)
-- Cek dulu data dokter
SELECT * FROM users WHERE id = 1;

-- Insert ke karyawan
INSERT INTO karyawan (nip, nama, alamat, no_hp, id_lokasi)
VALUES (
    '[NIP_DOKTER]',  -- dari query di atas
    '[NAMA_DOKTER]',
    'Alamat Dokter',
    '08123456789',
    1
);
```

---

### Query 4: Sync Semua User ke Karyawan (One-time Fix)

```sql
-- Backup dulu (optional)
CREATE TABLE karyawan_backup AS SELECT * FROM karyawan;

-- Sync all users with NIP to karyawan table
INSERT INTO karyawan (nip, nama, alamat, no_hp, id_lokasi)
SELECT 
    u.nip,
    COALESCE(u.name, 'User ' || u.nip) as nama,
    COALESCE(k_existing.alamat, '-') as alamat,
    COALESCE(k_existing.no_hp, '-') as no_hp,
    COALESCE(k_existing.id_lokasi, 1) as id_lokasi
FROM users u
LEFT JOIN karyawan k_existing ON u.nip = k_existing.nip
WHERE u.nip IS NOT NULL
AND k_existing.nip IS NULL  -- Hanya insert yang belum ada
ON DUPLICATE KEY UPDATE
    nama = VALUES(nama);  -- Update jika sudah ada tapi kosong
```

---

## 🧪 Testing Setelah Fix

### Option 1: Refresh Halaman (Dengan Fallback Logic)

1. **Hard refresh:**
   ```
   Ctrl + Shift + R
   ```

2. **Expected result:**
   - Jika `karyawan.nama` kosong → tampil `users.name`
   - Jika `users.name` juga kosong → tampil NIP
   - Minimal ada identifier, tidak tampil "-"

---

### Option 2: Fix Database Lalu Refresh

1. **Jalankan query fix di atas**

2. **Hard refresh:**
   ```
   Ctrl + Shift + R
   ```

3. **Expected result:**
   - Tampil nama lengkap dari `karyawan.nama`
   - Tidak lagi fallback ke NIP

---

## 📝 Rekomendasi

### Jangka Pendek (Sudah Applied):
✅ **Multi-level fallback** → Minimal tampil identifier (NIP/ID)

### Jangka Panjang (Database Fix):
⭐ **Sync semua users ke karyawan** → Data konsisten & complete

### Best Practice:
1. Setiap user yang dibuat **harus** punya entry di tabel karyawan
2. Field `karyawan.nama` **wajib diisi** (NOT NULL)
3. Validasi saat registrasi user baru
4. Background job untuk sync users ↔ karyawan

---

## 🎯 Expected Result Sekarang

Setelah fallback logic applied:

```
┌─────────────────────────────────────┐
│ No. RM:      3                      │
│ Tanggal:     08/10/2025 00:00       │
│ Pasien:      NIP: 198702142010... ✅│
│              (atau nama jika ada)   │
│ Dokter:      [Nama/NIP] ✅          │
└─────────────────────────────────────┘
```

**Tidak akan tampil "-" lagi** karena ada fallback ke NIP/ID!

---

## 💡 Quick Fix Command

Jalankan di terminal MySQL/phpMyAdmin:

```sql
-- Fix pasien NIP 198702142010333332
UPDATE karyawan 
SET nama = 'Nama Pasien' 
WHERE nip = '198702142010333332';

-- Fix dokter ID 1 (cek NIP dulu)
SELECT nip, name FROM users WHERE id = 1;

-- Lalu insert/update
INSERT INTO karyawan (nip, nama, alamat, no_hp, id_lokasi)
VALUES ('[NIP_HASIL_QUERY]', '[Nama Dokter]', '-', '-', 1)
ON DUPLICATE KEY UPDATE nama = '[Nama Dokter]';
```

---

## ✅ Status

- [x] Multi-level fallback added
- [x] View cache cleared
- [ ] **Refresh browser dan test** ← LAKUKAN INI
- [ ] (Optional) Fix database dengan query
- [ ] Remove debug info setelah OK

---

**Silakan refresh browser sekarang!** Nama seharusnya muncul (minimal NIP jika nama kosong). 🚀
