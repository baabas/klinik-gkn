# 🩺 SOLUTION: Nama Pasien & Dokter Tidak Muncul

## ✅ Fix yang Diterapkan

### Masalah:
- Field "Pasien" dan "Dokter" menampilkan "-"
- Nama tidak terdeteksi meskipun data ada

### Root Cause:
User **tidak punya data di tabel `karyawan`**, sehingga relasi `user.karyawan` return NULL.

### Solusi:
Tambahkan **fallback** ke `user.name` jika `user.karyawan` tidak ada.

---

## 🔧 Code Changes

### File: `resources/views/rekam-medis/print-resep.blade.php`

**BEFORE (Broken):**
```blade
{{-- Pasien --}}
@if($rekamMedis->pasien && $rekamMedis->pasien->karyawan)
    {{ $rekamMedis->pasien->karyawan->nama }}
@elseif($rekamMedis->pasienNonKaryawan)
    {{ $rekamMedis->pasienNonKaryawan->nama }}
@else
    -
@endif

{{-- Dokter --}}
@if($rekamMedis->dokter && $rekamMedis->dokter->karyawan)
    {{ $rekamMedis->dokter->karyawan->nama }}
@else
    -
@endif
```

**AFTER (Fixed):**
```blade
{{-- Pasien --}}
@if($rekamMedis->pasien && $rekamMedis->pasien->karyawan)
    {{ $rekamMedis->pasien->karyawan->nama }}
@elseif($rekamMedis->pasien)
    {{ $rekamMedis->pasien->name }}  ← FALLBACK ke users.name
@elseif($rekamMedis->pasienNonKaryawan)
    {{ $rekamMedis->pasienNonKaryawan->nama }}
@else
    -
@endif

{{-- Dokter --}}
@if($rekamMedis->dokter && $rekamMedis->dokter->karyawan)
    {{ $rekamMedis->dokter->karyawan->nama }}
@elseif($rekamMedis->dokter)
    {{ $rekamMedis->dokter->name }}  ← FALLBACK ke users.name
@else
    -
@endif
```

---

## 🎯 Logic Flow

### Pasien Name Resolution:

```
1. Cek rekamMedis.pasien.karyawan.nama
   ↓ (jika ada)
   RETURN nama dari tabel karyawan
   
2. Cek rekamMedis.pasien.name  ← NEW!
   ↓ (jika ada)
   RETURN name dari tabel users (fallback)
   
3. Cek rekamMedis.pasienNonKaryawan.nama
   ↓ (jika ada)
   RETURN nama dari tabel non_karyawan
   
4. Jika semua NULL
   ↓
   RETURN "-"
```

### Dokter Name Resolution:

```
1. Cek rekamMedis.dokter.karyawan.nama
   ↓ (jika ada)
   RETURN nama dari tabel karyawan
   
2. Cek rekamMedis.dokter.name  ← NEW!
   ↓ (jika ada)
   RETURN name dari tabel users (fallback)
   
3. Jika semua NULL
   ↓
   RETURN "-"
```

---

## 📊 Skenario yang Ditangani

### ✅ Skenario 1: User punya data karyawan (Ideal)
```
users.nip = '123456'
karyawan.nip = '123456'
karyawan.nama = 'Dr. Jane Smith'

Result: "Dr. Jane Smith" ✅
```

### ✅ Skenario 2: User TIDAK punya data karyawan (Fixed!)
```
users.id = 3
users.name = 'Dr. John Doe'
karyawan: (tidak ada row dengan nip ini)

Result sebelumnya: "-" ❌
Result sekarang: "Dr. John Doe" ✅ (fallback ke users.name)
```

### ✅ Skenario 3: Pasien non-karyawan
```
rekam_medis.nik_pasien = '1987021420103333'
non_karyawan.nik = '1987021420103333'
non_karyawan.nama = 'Jane Doe'

Result: "Jane Doe" ✅
```

### ✅ Skenario 4: Data tidak ada sama sekali
```
rekam_medis.id_dokter = NULL
OR
rekam_medis.nip_pasien = NULL AND nik_pasien = NULL

Result: "-" ✅ (expected behavior)
```

---

## 🧪 Testing

### Test Case 1: User dengan Karyawan
**Setup:**
```sql
-- User dokter
INSERT INTO users (id, nip, name, password, id_role) 
VALUES (3, '123456', 'Dr. Smith', 'xxx', 1);

-- Karyawan dokter
INSERT INTO karyawan (nip, nama, alamat, no_hp) 
VALUES ('123456', 'Dr. Jane Smith', 'Jl. A', '08111');
```

**Expected Output:**
```
Dokter: Dr. Jane Smith
```

**Reason:** Karyawan data exists, use `karyawan.nama`

---

### Test Case 2: User TANPA Karyawan (FIXED!)
**Setup:**
```sql
-- User dokter (tidak ada di tabel karyawan)
INSERT INTO users (id, nip, name, password, id_role) 
VALUES (5, '999999', 'Dr. John Doe', 'xxx', 1);
```

**Expected Output:**
```
Dokter: Dr. John Doe
```

**Reason:** Karyawan data NOT exists, fallback to `users.name`

---

### Test Case 3: Pasien Non-Karyawan
**Setup:**
```sql
-- Pasien non-karyawan
INSERT INTO non_karyawan (nik, nama, alamat, no_hp) 
VALUES ('1987021420103333', 'Jane Doe', 'Jl. B', '08222');

-- Rekam medis
INSERT INTO rekam_medis (..., nik_pasien, ...) 
VALUES (..., '1987021420103333', ...);
```

**Expected Output:**
```
Pasien: Jane Doe
```

**Reason:** Non-karyawan data exists, use `non_karyawan.nama`

---

## 🔍 Debug Info

Saya sudah menambahkan **debug info box** di halaman print resep.

**Cara melihat:**
1. Refresh halaman print resep
2. Lihat kotak abu-abu dengan info debug
3. Cek value dari:
   - `pasien_exists`
   - `pasien_karyawan_exists`
   - `pasien_karyawan_nama`
   - `dokter_exists`
   - `dokter_karyawan_exists`
   - `dokter_karyawan_nama`

**Jika masih "-" setelah fix:**
- Cek `pasien_exists` = "NO" → Relasi user pasien broken
- Cek `dokter_exists` = "NO" → id_dokter NULL atau user tidak ada
- Screenshot debug info dan kirim

---

## 🎯 Expected Result Setelah Fix

### Print Preview Seharusnya:
```
┌─────────────────────────────────────┐
│ KLINIK GKN                          │
│ RESEP OBAT                          │
│ ─────────────────────────────       │
│ No. RM:      RM                     │
│ Tanggal:     08/10/2025 00:00       │
│ Pasien:      [NAMA PASIEN] ✅       │
│ Dokter:      [NAMA DOKTER] ✅       │
│ ─────────────────────────────       │
│ 1. Flucadex                         │
│    Jumlah: 4 Tablet                 │
│    Dosis: 2x1                       │
└─────────────────────────────────────┘
```

**Tidak boleh:**
```
Pasien:      -  ❌
Dokter:      -  ❌
```

---

## 📝 Next Steps

1. **Refresh Browser:**
   ```
   Ctrl + Shift + R (hard refresh)
   ```

2. **Cek Print Resep:**
   - Akses rekam medis yang sudah ada
   - Klik print resep
   - Lihat apakah nama muncul

3. **Cek Debug Info:**
   - Scroll ke kotak abu-abu debug
   - Verifikasi semua data ter-load

4. **Test Buat Rekam Medis Baru:**
   - Buat rekam medis baru
   - Input resep obat
   - Save dan print
   - Nama harus muncul otomatis

5. **Hapus Debug Info (Setelah Selesai):**
   - Comment out section debug di view
   - Atau saya akan hapus nanti

---

## 💡 Permanent Solution (Optional)

Untuk jangka panjang, sebaiknya:

### Option 1: Isi Data Karyawan untuk Semua User

```sql
-- Sync semua user ke tabel karyawan
INSERT INTO karyawan (nip, nama, alamat, no_hp, id_lokasi)
SELECT 
    nip,
    name as nama,
    '-' as alamat,
    '-' as no_hp,
    1 as id_lokasi
FROM users
WHERE nip IS NOT NULL
AND nip NOT IN (SELECT nip FROM karyawan);
```

### Option 2: Gunakan `users.name` Sebagai Default

Tetap gunakan fallback logic seperti sekarang (sudah fixed).

### Option 3: Buat Virtual Column

```php
// Di model User
public function getDisplayNameAttribute()
{
    return $this->karyawan->nama ?? $this->name;
}
```

Lalu di view:
```blade
{{ $rekamMedis->dokter->display_name }}
```

---

## ✅ Status

- [x] Fallback logic added
- [x] View cache cleared
- [x] Debug info displayed
- [ ] User testing (refresh dan cek)
- [ ] Screenshot hasil
- [ ] Remove debug info after verified

---

**File:** SOLUTION_NAMA_PASIEN_DOKTER.md  
**Status:** Fix applied, awaiting user testing  
**Last Updated:** 08 Oktober 2025 - 07:10 WIB
