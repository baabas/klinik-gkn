# 🔍 Debug Script - Test Rekam Medis Data

## Quick Test dengan Tinker

Jalankan command ini untuk debug:

```bash
php artisan tinker
```

Lalu jalankan script berikut:

```php
// 1. Ambil rekam medis terakhir
$rm = App\Models\RekamMedis::latest()->first();

// 2. Cek ID dan foreign keys
echo "ID Rekam Medis: " . $rm->id_rekam_medis . "\n";
echo "NIP Pasien: " . ($rm->nip_pasien ?? 'NULL') . "\n";
echo "NIK Pasien: " . ($rm->nik_pasien ?? 'NULL') . "\n";
echo "ID Dokter: " . ($rm->id_dokter ?? 'NULL') . "\n";

// 3. Test load dengan eager loading
$rm = App\Models\RekamMedis::with(['dokter.karyawan', 'pasien.karyawan', 'pasienNonKaryawan'])->latest()->first();

// 4. Cek relasi dokter
echo "\n--- DOKTER ---\n";
if ($rm->dokter) {
    echo "Dokter User ID: " . $rm->dokter->id . "\n";
    echo "Dokter NIP: " . ($rm->dokter->nip ?? 'NULL') . "\n";
    echo "Dokter NIK: " . ($rm->dokter->nik ?? 'NULL') . "\n";
    echo "Dokter Name: " . $rm->dokter->name . "\n";
    
    if ($rm->dokter->karyawan) {
        echo "Dokter Karyawan Nama: " . $rm->dokter->karyawan->nama . "\n";
    } else {
        echo "Dokter Karyawan: NULL\n";
    }
} else {
    echo "Dokter: NULL\n";
}

// 5. Cek relasi pasien
echo "\n--- PASIEN ---\n";
if ($rm->pasien) {
    echo "Pasien User ID: " . $rm->pasien->id . "\n";
    echo "Pasien NIP: " . ($rm->pasien->nip ?? 'NULL') . "\n";
    echo "Pasien Name: " . $rm->pasien->name . "\n";
    
    if ($rm->pasien->karyawan) {
        echo "Pasien Karyawan Nama: " . $rm->pasien->karyawan->nama . "\n";
    } else {
        echo "Pasien Karyawan: NULL\n";
    }
} else {
    echo "Pasien: NULL\n";
}

// 6. Cek pasien non-karyawan
if ($rm->pasienNonKaryawan) {
    echo "Pasien Non-Karyawan Nama: " . $rm->pasienNonKaryawan->nama . "\n";
} else {
    echo "Pasien Non-Karyawan: NULL\n";
}
```

---

## Query SQL Langsung

Atau gunakan query SQL ini:

```sql
-- 1. Cek data rekam_medis terakhir
SELECT * FROM rekam_medis ORDER BY id_rekam_medis DESC LIMIT 1;

-- 2. Cek data user dokter (ganti [ID_DOKTER] dengan hasil query 1)
SELECT 
    u.id, 
    u.nip, 
    u.nik, 
    u.name, 
    k.nama as nama_karyawan,
    nk.nama as nama_non_karyawan
FROM users u
LEFT JOIN karyawan k ON u.nip = k.nip
LEFT JOIN non_karyawan nk ON u.nik = nk.nik
WHERE u.id = [ID_DOKTER];

-- 3. Cek data user pasien karyawan (jika nip_pasien ada)
SELECT 
    u.id, 
    u.nip, 
    u.name, 
    k.nama as nama_karyawan
FROM users u
LEFT JOIN karyawan k ON u.nip = k.nip
WHERE u.nip = '[NIP_PASIEN]';

-- 4. Cek data pasien non-karyawan (jika nik_pasien ada)
SELECT * FROM non_karyawan WHERE nik = '[NIK_PASIEN]';
```

---

## Expected vs Actual

### ✅ Expected (Should Work):

**Dokter:**
```
dokter.id = 3
dokter.nip = '123456'
dokter.karyawan.nip = '123456'
dokter.karyawan.nama = 'Dr. Jane Smith'
```

**Pasien Karyawan:**
```
pasien.nip = '789012'
pasien.karyawan.nip = '789012'
pasien.karyawan.nama = 'John Doe'
```

**Pasien Non-Karyawan:**
```
pasienNonKaryawan.nik = '1987021420103333'
pasienNonKaryawan.nama = 'Jane Doe'
```

### ❌ Actual (If Broken):

**Kemungkinan 1: Data karyawan tidak ada**
```
dokter.karyawan = NULL
pasien.karyawan = NULL
```

**Kemungkinan 2: Relasi NIP tidak match**
```
dokter.nip = '123456'
karyawan.nip = '123456789' (different!)
```

**Kemungkinan 3: id_dokter NULL**
```
rekam_medis.id_dokter = NULL
```

---

## Cara Perbaiki

### Fix 1: Insert Data Karyawan untuk Dokter

```sql
-- Cek NIP dokter
SELECT id, nip, name FROM users WHERE id = [ID_DOKTER];

-- Insert ke tabel karyawan
INSERT INTO karyawan (nip, nama, alamat, no_hp, id_lokasi)
VALUES (
    '[NIP_DOKTER]',  -- Harus sama persis dengan users.nip
    '[NAMA_DOKTER]',
    'Alamat Dokter',
    '08123456789',
    1  -- ID lokasi klinik
);
```

### Fix 2: Insert Data Karyawan untuk Pasien

```sql
-- Cek NIP pasien
SELECT id, nip, name FROM users WHERE nip = '[NIP_PASIEN]';

-- Insert ke tabel karyawan
INSERT INTO karyawan (nip, nama, alamat, no_hp, id_lokasi)
VALUES (
    '[NIP_PASIEN]',  -- Harus sama persis dengan users.nip
    '[NAMA_PASIEN]',
    'Alamat Pasien',
    '08987654321',
    1  -- ID lokasi klinik
);
```

### Fix 3: Update id_dokter Jika NULL

```sql
-- Update manual (HATI-HATI!)
UPDATE rekam_medis 
SET id_dokter = [ID_USER_DOKTER] 
WHERE id_rekam_medis = [ID_REKAM_MEDIS] 
AND id_dokter IS NULL;
```

---

## Testing Setelah Fix

1. **Jalankan tinker script lagi**
2. **Refresh halaman print resep**
3. **Cek debug info** (kotak abu-abu)
4. **Verifikasi nama muncul**

---

**File ini:** Debug helper untuk troubleshooting nama pasien & dokter  
**Status:** Temporary - Hapus setelah masalah resolved
