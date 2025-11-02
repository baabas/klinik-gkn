# 📱 Panduan Feedback Pasien - Sistem Multi Lokasi

## 🎯 Tujuan
Menghindari konflik feedback ketika ada pasien yang berobat di klinik berbeda. Setiap tablet di lokasi tertentu hanya akan menampilkan feedback request untuk pasien yang berobat di lokasi tersebut saja.

---

## 🔧 Implementasi Teknis

### **1. Database Schema**

#### Tabel `users` (Dokter sudah punya kolom `id_lokasi`)
```sql
-- Sudah ada, tidak perlu migration baru!
users.id_lokasi → Foreign Key ke lokasi_klinik.id
```

**Keuntungan Solusi Ini:**
✅ Tidak perlu tambah kolom baru di `rekam_medis`
✅ Lokasi otomatis dari dokter yang input rekam medis
✅ Normalisasi database lebih baik
✅ Query menggunakan relasi eloquent

**Struktur Relasi:**
```
rekam_medis → dokter (users) → id_lokasi → lokasi_klinik
```

---

### **2. Model Update**

#### **RekamMedis.php** (Tidak perlu update fillable)
Model sudah memiliki relasi `dokter()`, cukup gunakan untuk akses lokasi:

```php
// Cara akses lokasi dari rekam medis:
$rekamMedis->dokter->id_lokasi
$rekamMedis->dokter->lokasi->nama_lokasi
```

---

### **3. Controller Update**

#### **FeedbackController.php**

##### **showFeedbackForm()** - Menampilkan Form Feedback
```php
public function showFeedbackForm(Request $request)
{
    $idLokasi = $request->query('lokasi');
    
    if (!$idLokasi) {
        abort(400, 'Parameter lokasi harus diisi. Contoh: /feedback/form?lokasi=1');
    }
    
    // Ambil rekam medis HANYA untuk dokter di lokasi ini
    $pendingFeedback = RekamMedis::whereDate('tanggal_kunjungan', today())
        ->whereDoesntHave('feedback')
        ->whereHas('dokter', function($query) use ($idLokasi) {
            $query->where('id_lokasi', $idLokasi); // <-- FILTER via relasi
        })
        ->with(['pasien.karyawan', 'pasienNonKaryawan', 'dokter'])
        ->orderBy('created_at', 'desc')
        ->first();

    return view('feedback.form', compact('pendingFeedback', 'lokasi'));
}
```

**Penjelasan Query:**
- `whereHas('dokter')` → Filtering melalui relasi ke tabel `users`
- `where('id_lokasi', $idLokasi)` → Filter dokter berdasarkan lokasi
- **Hasil:** Hanya rekam medis dari dokter di lokasi tertentu yang muncul

##### **checkPendingFeedback()** - API Polling untuk Tablet
```php
public function checkPendingFeedback(Request $request)
{
    $idLokasi = $request->query('lokasi');
    
    if (!$idLokasi) {
        return response()->json([
            'error' => true,
            'message' => 'Parameter lokasi harus diisi.'
        ], 400);
    }
    
    // Cari pending feedback HANYA untuk dokter di lokasi ini
    $pending = RekamMedis::whereDate('tanggal_kunjungan', today())
        ->whereDoesntHave('feedback')
        ->whereHas('dokter', function($query) use ($idLokasi) {
            $query->where('id_lokasi', $idLokasi);
        })
        ->with(['pasien.karyawan', 'pasienNonKaryawan.user'])
        ->orderBy('created_at', 'desc')
        ->first();
    
    // Return JSON response
}
```

---

## 🖥️ Cara Penggunaan

### **Setup Tablet di Setiap Lokasi**

#### **Klinik 1 (ID Lokasi: 1)**
```
URL: http://klinik.app/feedback/form?lokasi=1
```

#### **Klinik 2 (ID Lokasi: 2)**
```
URL: http://klinik.app/feedback/form?lokasi=2
```

#### **Klinik Pusat (ID Lokasi: 3)**
```
URL: http://klinik.app/feedback/form?lokasi=3
```

---

## 🧪 Skenario Testing

### **Skenario 1: Pasien Berobat di Klinik 1**
1. Dokter di **Klinik 1** input rekam medis dengan `id_lokasi = 1`
2. Tablet di **Klinik 1** akan menampilkan form feedback untuk pasien ini ✅
3. Tablet di **Klinik 2** TIDAK akan menampilkan form feedback ini ✅

### **Skenario 2: Pasien Berobat di Klinik 2**
1. Dokter di **Klinik 2** input rekam medis dengan `id_lokasi = 2`
2. Tablet di **Klinik 2** akan menampilkan form feedback untuk pasien ini ✅
3. Tablet di **Klinik 1** TIDAK akan menampilkan form feedback ini ✅

### **Skenario 3: Multi Pasien di Lokasi Berbeda**
**Waktu: 10:00 - Pasien A berobat di Klinik 1**
- Rekam Medis A: `id_lokasi = 1`
- Tablet Klinik 1: Menampilkan Pasien A ✅
- Tablet Klinik 2: Tidak menampilkan ✅

**Waktu: 10:05 - Pasien B berobat di Klinik 2**
- Rekam Medis B: `id_lokasi = 2`
- Tablet Klinik 1: Masih menampilkan Pasien A ✅
- Tablet Klinik 2: Menampilkan Pasien B ✅

**Kesimpulan:** Tidak ada konflik! ✅

---

## ⚠️ Hal yang Perlu Diperhatikan

### **1. Saat Input Rekam Medis**
**TIDAK PERLU** input lokasi manual karena sudah otomatis dari dokter yang login!

Dokter login → `Auth::user()->id_lokasi` → Otomatis tersimpan di relasi

### **2. Setup Dokter**
Pastikan setiap dokter sudah di-assign ke lokasi klinik:
```sql
-- Cek dokter yang belum punya lokasi
SELECT id, name, email, id_lokasi 
FROM users 
WHERE akses LIKE '%DOKTER%' AND id_lokasi IS NULL;

-- Assign lokasi ke dokter
UPDATE users SET id_lokasi = 1 WHERE id = 123; -- Dokter di Klinik 1
UPDATE users SET id_lokasi = 2 WHERE id = 456; -- Dokter di Klinik 2
```

### **3. Validasi (Tidak Diperlukan)**
Karena menggunakan relasi, tidak perlu validasi tambahan di form rekam medis

---

## 📊 Query untuk Monitoring

### **Cek Rekam Medis Berdasarkan Lokasi**
```sql
SELECT 
    rm.id_rekam_medis,
    rm.tanggal_kunjungan,
    lk.nama_lokasi,
    u.name AS nama_dokter,
    COALESCE(u2.nama_karyawan, nk.nama_lengkap) AS nama_pasien,
    CASE WHEN fp.id_feedback IS NOT NULL THEN 'Sudah' ELSE 'Belum' END AS status_feedback
FROM rekam_medis rm
JOIN users u ON rm.id_dokter = u.id
LEFT JOIN lokasi_klinik lk ON u.id_lokasi = lk.id
LEFT JOIN users u2 ON rm.nip_pasien = u2.nip
LEFT JOIN non_karyawan nk ON rm.nik_pasien = nk.nik
LEFT JOIN feedback_pasien fp ON rm.id_rekam_medis = fp.id_rekam_medis
WHERE DATE(rm.tanggal_kunjungan) = CURDATE()
ORDER BY rm.created_at DESC;
```

### **Cek Pending Feedback per Lokasi**
```sql
SELECT 
    lk.id AS id_lokasi,
    lk.nama_lokasi,
    COUNT(rm.id_rekam_medis) AS pending_feedback
FROM lokasi_klinik lk
LEFT JOIN users u ON lk.id = u.id_lokasi
LEFT JOIN rekam_medis rm ON u.id = rm.id_dokter
    AND DATE(rm.tanggal_kunjungan) = CURDATE()
    AND rm.id_rekam_medis NOT IN (SELECT id_rekam_medis FROM feedback_pasien)
GROUP BY lk.id, lk.nama_lokasi;
```

---

## 🚀 Next Steps

### **1. Setup Lokasi Dokter**
Pastikan setiap dokter sudah punya `id_lokasi` di tabel users

### **2. Shortcut URL**
Buat shortcut di desktop setiap tablet:
- **Tablet Klinik 1**: `Feedback - Klinik 1.url` → `http://klinik.app/feedback/form?lokasi=1`
- **Tablet Klinik 2**: `Feedback - Klinik 2.url` → `http://klinik.app/feedback/form?lokasi=2`

### **3. Testing**
Test dengan:
1. Login sebagai dokter di Klinik 1
2. Input rekam medis pasien
3. Buka tablet Klinik 1 → Seharusnya muncul feedback request ✅
4. Buka tablet Klinik 2 → Seharusnya TIDAK muncul ✅

---

## 📝 Summary

✅ **Masalah:** Tablet di semua lokasi menampilkan feedback yang sama  
✅ **Solusi:** Filter rekam medis berdasarkan `dokter.id_lokasi` (melalui relasi)  
✅ **Keuntungan:** 
   - Tidak perlu kolom baru di `rekam_medis`
   - Lokasi otomatis dari dokter yang login
   - Lebih efisien dan normalized
✅ **Cara Pakai:** Tablet akses URL dengan parameter `?lokasi=X`  
✅ **Hasil:** Tidak ada konflik feedback antar lokasi!

---

**Dibuat:** 10 Oktober 2025  
**Status:** ✅ Implementasi Selesai (Menggunakan Relasi Eloquent)  
**Keuntungan:** Tidak perlu migration baru, menggunakan struktur database yang sudah ada!
