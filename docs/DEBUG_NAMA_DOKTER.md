# 🩺 Debug: Nama Dokter Tidak Muncul di Struk Resep

## 🔍 Analisis Masalah

### Gejala:
- Pada struk resep obat, field **"Dokter"** menampilkan **tanda "-"**
- Nama dokter tidak terdeteksi meskipun data tersimpan

### Kemungkinan Penyebab:

#### 1. ❌ Data `id_dokter` Tidak Tersimpan
**Cek di database:**
```sql
SELECT id_rekam_medis, id_dokter, nip_pasien, nik_pasien 
FROM rekam_medis 
ORDER BY id_rekam_medis DESC 
LIMIT 5;
```

**Expected output:**
```
| id_rekam_medis | id_dokter | nip_pasien | nik_pasien |
|----------------|-----------|------------|------------|
| 2              | 3         | NULL       | 1987...    |
```

**Jika `id_dokter` NULL:**
- ❌ **Masalah:** Controller tidak save `id_dokter`
- ✅ **Solusi:** Sudah diperbaiki di `RekamMedisController.php` line 54
  ```php
  'id_dokter' => Auth::id(),
  ```

---

#### 2. ❌ Relasi Model Tidak Benar
**File:** `app/Models/RekamMedis.php`

**Cek relasi:**
```php
public function dokter()
{
    return $this->belongsTo(User::class, 'id_dokter');
}
```

**Expected:** ✅ Relasi sudah benar
- Foreign key: `id_dokter`
- References: `users.id`

---

#### 3. ❌ Eager Loading Tidak Lengkap
**File:** `app/Http/Controllers/RekamMedisController.php`

**Cek query print resep (line 182-187):**
```php
$rekamMedis = RekamMedis::with([
    'resepObat.obat',
    'dokter.karyawan',        // ✅ Dokter + profil
    'pasien.karyawan',        // ✅ Pasien karyawan
    'pasienNonKaryawan'       // ✅ Pasien non-karyawan
])->findOrFail($id);
```

**Expected:** ✅ Eager loading sudah lengkap

---

#### 4. ❌ User Tidak Punya Profil Karyawan
**Kemungkinan:** User dokter tidak terhubung ke tabel `karyawan`

**Cek di database:**
```sql
SELECT u.id, u.nip, u.nik, u.name, k.nama as nama_karyawan
FROM users u
LEFT JOIN karyawan k ON u.nip = k.nip
WHERE u.id = [ID_DOKTER];
```

**Expected output:**
```
| id | nip    | nik  | name     | nama_karyawan |
|----|--------|------|----------|---------------|
| 3  | 123456 | NULL | Dr. John | Dr. John      |
```

**Jika `nama_karyawan` NULL:**
- ❌ **Masalah:** Data karyawan tidak ada untuk NIP dokter
- ✅ **Solusi:** Insert data ke tabel `karyawan`
  ```sql
  INSERT INTO karyawan (nip, nama, alamat, no_hp) 
  VALUES ('123456', 'Dr. John Doe', 'Jl. Example', '08123456789');
  ```

---

#### 5. ❌ View Blade Logic Error
**File:** `resources/views/rekam-medis/print-resep.blade.php`

**Cek logic (line 213-220):**
```blade
<div class="info-row">
    <span class="info-label">Dokter</span>
    <span class="info-value">
        @if($rekamMedis->dokter && $rekamMedis->dokter->karyawan)
            {{ $rekamMedis->dokter->karyawan->nama }}
        @else
            -
        @endif
    </span>
</div>
```

**Debug steps:**
1. Cek `$rekamMedis->dokter` → Harus ada (object User)
2. Cek `$rekamMedis->dokter->karyawan` → Harus ada (object Karyawan)
3. Cek `$rekamMedis->dokter->karyawan->nama` → Harus string nama

---

## 🛠️ Cara Debug Manual

### Step 1: Tambahkan Debug di View (Temporary)

**File:** `resources/views/rekam-medis/print-resep.blade.php`

**Tambahkan sebelum `<div class="info-row">` Dokter:**
```blade
{{-- DEBUG: HAPUS SETELAH SELESAI --}}
@php
    dump([
        'rekamMedis.id_dokter' => $rekamMedis->id_dokter,
        'rekamMedis.dokter' => $rekamMedis->dokter ? 'EXISTS' : 'NULL',
        'rekamMedis.dokter.nip' => $rekamMedis->dokter->nip ?? 'NULL',
        'rekamMedis.dokter.karyawan' => $rekamMedis->dokter->karyawan ?? 'NULL',
        'rekamMedis.dokter.karyawan.nama' => $rekamMedis->dokter->karyawan->nama ?? 'NULL',
    ]);
@endphp
```

**Akses print resep → Lihat output debug di browser**

---

### Step 2: Cek Database Langsung

**Query 1: Cek rekam medis**
```sql
SELECT * FROM rekam_medis WHERE id_rekam_medis = [ID_RESEP];
```

**Query 2: Cek user dokter**
```sql
SELECT u.*, k.nama 
FROM users u
LEFT JOIN karyawan k ON u.nip = k.nip
WHERE u.id = [ID_DOKTER_DARI_QUERY_1];
```

**Query 3: Cek role dokter**
```sql
SELECT u.id, u.name, r.nama_role
FROM users u
JOIN roles r ON u.id_role = r.id_role
WHERE u.id = [ID_DOKTER];
```

---

### Step 3: Test dengan Tinker

```bash
php artisan tinker
```

```php
// Load rekam medis dengan relasi
$rm = App\Models\RekamMedis::with(['dokter.karyawan'])->find(2);

// Cek dokter
$rm->dokter; // Harus return User object

// Cek karyawan dokter
$rm->dokter->karyawan; // Harus return Karyawan object

// Cek nama
$rm->dokter->karyawan->nama; // Harus return string nama
```

---

## ✅ Solusi Berdasarkan Penyebab

### Jika Data `id_dokter` NULL di Database:

**Fix:** Tambahkan manual atau buat ulang rekam medis

```sql
-- Update manual (HATI-HATI!)
UPDATE rekam_medis 
SET id_dokter = [ID_USER_DOKTER] 
WHERE id_rekam_medis = [ID_RESEP];
```

**Atau:** Buat rekam medis baru (sudah auto-save dengan code terbaru)

---

### Jika User Dokter Tidak Punya Data Karyawan:

**Fix:** Insert data karyawan untuk user dokter

```sql
-- Cek NIP dokter
SELECT id, nip, name FROM users WHERE id = [ID_DOKTER];

-- Insert ke karyawan
INSERT INTO karyawan (nip, nama, alamat, no_hp, id_lokasi) 
VALUES (
    '[NIP_DOKTER]', 
    '[NAMA_DOKTER]', 
    '[ALAMAT]', 
    '[NO_HP]',
    1  -- ID lokasi klinik
);
```

---

### Jika Relasi Model Salah:

**File:** `app/Models/User.php`

**Pastikan ada relasi:**
```php
public function karyawan()
{
    return $this->hasOne(Karyawan::class, 'nip', 'nip');
}
```

**Clear cache setelah edit model:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

### Jika Eager Loading Tidak Jalan:

**File:** `app/Http/Controllers/RekamMedisController.php`

**Pastikan query benar:**
```php
public function printResep($id)
{
    $rekamMedis = RekamMedis::with([
        'resepObat.obat',
        'dokter.karyawan',        // ✅ HARUS ADA
        'pasien.karyawan',
        'pasienNonKaryawan'
    ])->findOrFail($id);

    return view('rekam-medis.print-resep', compact('rekamMedis'));
}
```

---

## 🧪 Testing Final

### Test Case 1: Dokter Karyawan (NIP-based)
```
User dokter dengan NIP → Join tabel karyawan
Expected: Nama dokter muncul di struk
```

### Test Case 2: Dokter Non-Karyawan (NIK-based)
```
User dokter dengan NIK → Join tabel non_karyawan? (unusual)
Expected: Dokter biasanya karyawan, jadi harus punya NIP
```

### Test Case 3: Multiple Dokter
```
Buat resep dengan 2-3 dokter berbeda
Expected: Setiap resep tampil nama dokternya masing-masing
```

---

## 📊 Expected vs Actual

### Expected Output di Struk:
```
No. RM:      2
Tanggal:     08/10/2025 09:00
Pasien:      John Doe
Dokter:      Dr. Jane Smith  ← HARUS MUNCUL
```

### Actual Output (Jika Error):
```
No. RM:      2
Tanggal:     08/10/2025 09:00
Pasien:      John Doe
Dokter:      -               ← TIDAK MUNCUL
```

---

## 🎯 Checklist Verifikasi

Setelah troubleshooting, pastikan:

- [ ] Field `id_dokter` di tabel `rekam_medis` **NOT NULL**
- [ ] Relasi `dokter()` di model `RekamMedis` **return User**
- [ ] Relasi `karyawan()` di model `User` **return Karyawan**
- [ ] User dokter **punya data di tabel karyawan**
- [ ] Eager loading `.dokter.karyawan` **di controller**
- [ ] Blade view **cek kondisi dengan benar**
- [ ] Test print resep **tampil nama dokter**
- [ ] Multiple dokter **masing-masing tampil benar**

---

## 📞 Jika Masih Error

**Lakukan:**
1. Jalankan debug di Step 1-3
2. Screenshot output debug
3. Copy-paste query SQL hasil
4. Cek Laravel log: `storage/logs/laravel.log`
5. Report dengan info lengkap:
   - ID rekam medis yang bermasalah
   - Output debug dari view
   - Query result dari database
   - Laravel error log (jika ada)

**Common Mistakes:**
- ❌ Lupa run `php artisan view:clear`
- ❌ Lupa hard refresh browser (Ctrl+Shift+R)
- ❌ Data karyawan belum ada
- ❌ User dokter login dengan NIK bukan NIP
- ❌ Relasi model typo atau salah foreign key

---

**Status:** ✅ Code sudah benar, jika masih error kemungkinan masalah data
**Last Updated:** 08 Oktober 2025 - 06:51 WIB
