# Log Distribusi Barang - Dokumentasi

## Ringkasan
Fitur log distribusi barang adalah sistem audit trail yang mencatat semua distribusi barang antar lokasi klinik. Fitur ini mengimplementasikan **Opsi B** - sistem auto-approval dengan log dan audit trail lengkap.

## Cara Kerja

### 1. Auto-Logging Distribusi
- Setiap kali DOKTER melakukan distribusi barang antar klinik, sistem akan **otomatis mencatat** ke tabel `distribusi_barang`
- Status distribusi diatur sebagai `approved` (disetujui otomatis)
- Stok barang langsung dipindahkan tanpa menunggu approval
- Cocok untuk kebutuhan emergency/mendesak

### 2. Role-Based Access

#### PENGADAAN (Full Access)
- ✅ Melihat **semua** log distribusi dari semua lokasi
- ✅ Filter berdasarkan tanggal, lokasi, barang, status
- ✅ Melihat detail distribusi
- ✅ Export ke Excel (placeholder - akan diimplementasikan)
- ✅ Validasi distribusi yang berstatus `pending` (approve/reject)

#### DOKTER (Read-Only)
- ✅ Melihat log distribusi yang dilakukan sendiri
- ✅ Melihat distribusi yang melibatkan lokasi klinik mereka
- ✅ Filter berdasarkan tanggal, lokasi, barang, status
- ✅ Melihat detail distribusi (hanya yang relevan)
- ❌ Tidak bisa approve/reject
- ❌ Tidak bisa export

## Struktur Database

### Tabel: `distribusi_barang`

```sql
CREATE TABLE distribusi_barang (
    id_distribusi BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_barang BIGINT NOT NULL,           -- FK ke barang_medis
    id_lokasi_asal BIGINT NOT NULL,      -- FK ke lokasi_klinik
    id_lokasi_tujuan BIGINT NOT NULL,    -- FK ke lokasi_klinik
    id_user BIGINT NOT NULL,             -- FK ke users (distributor)
    jumlah INT NOT NULL,
    keterangan TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
    validated_by BIGINT,                 -- FK ke users (PENGADAAN)
    validated_at TIMESTAMP,
    validation_note TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (id_barang) REFERENCES barang_medis(id_obat),
    FOREIGN KEY (id_lokasi_asal) REFERENCES lokasi_klinik(id),
    FOREIGN KEY (id_lokasi_tujuan) REFERENCES lokasi_klinik(id),
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (validated_by) REFERENCES users(id),
    
    -- Indexes untuk performa
    INDEX idx_barang (id_barang),
    INDEX idx_lokasi_asal (id_lokasi_asal),
    INDEX idx_lokasi_tujuan (id_lokasi_tujuan),
    INDEX idx_user (id_user),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

## File yang Diubah/Dibuat

### Migration
- `database/migrations/2025_10_22_024945_create_distribusi_barang_table.php`

### Model
- `app/Models/DistribusiBarang.php` (NEW)
  - Relations: `barang()`, `lokasiAsal()`, `lokasiTujuan()`, `user()`, `validator()`

### Controller
- `app/Http/Controllers/DistribusiBarangController.php` (NEW)
  - `index()` - List log dengan filtering role-based
  - `show()` - Detail distribusi dengan access control
  - `validate()` - Approve/reject (PENGADAAN only)
  - `export()` - Export Excel (placeholder)
  
- `app/Http/Controllers/BarangMedisController.php` (UPDATED)
  - Method `distribusi()` - Ditambahkan logging ke `distribusi_barang`

### Routes
- `routes/web.php` (UPDATED)
  - PENGADAAN: 4 routes (index, show, validate, export)
  - DOKTER: 2 routes (index, show)

### Views
- `resources/views/distribusi-barang/index.blade.php` (NEW)
  - Tabel log distribusi dengan pagination
  - Filter: tanggal, lokasi asal, lokasi tujuan, barang, status
  - Tombol Export Excel untuk PENGADAAN
  
- `resources/views/distribusi-barang/show.blade.php` (NEW)
  - Detail distribusi lengkap
  - Timeline tracking
  - Form validasi untuk PENGADAAN (jika status pending)

### Navigation
- `resources/views/layouts/navigation-sidebar.blade.php` (UPDATED)
  - Menu "Log Distribusi" untuk DOKTER
  - Menu "Log Distribusi" untuk PENGADAAN

## Cara Menggunakan

### Untuk DOKTER

1. **Melakukan Distribusi**
   - Buka menu "Obat & Alat Medis"
   - Pilih barang yang akan didistribusikan
   - Klik "Distribusi ke Klinik Lain"
   - Isi form dan submit
   - ✅ Distribusi akan otomatis tercatat di log

2. **Melihat Log Distribusi**
   - Buka menu "Log Distribusi"
   - Anda akan melihat:
     * Distribusi yang Anda lakukan sendiri
     * Distribusi dari/ke lokasi klinik Anda
   - Gunakan filter untuk mencari distribusi tertentu
   - Klik "Detail" untuk melihat informasi lengkap

### Untuk PENGADAAN

1. **Melihat Semua Distribusi**
   - Buka menu "Log Distribusi"
   - Anda akan melihat **semua** distribusi dari semua lokasi
   - Gunakan filter untuk analisis:
     * Filter tanggal untuk laporan periode tertentu
     * Filter lokasi untuk audit klinik tertentu
     * Filter barang untuk tracking item spesifik
     * Filter status untuk melihat pending/approved/rejected

2. **Validasi Distribusi (Opsional)**
   - Klik "Detail" pada distribusi yang berstatus `pending`
   - Isi catatan validasi (opsional)
   - Klik "Setujui" atau "Tolak"
   - Jika ditolak, stok akan otomatis dikembalikan

3. **Export Data**
   - Klik tombol "Export Excel" di halaman list
   - (Fitur ini placeholder - akan diimplementasikan)

## Validasi & Reversal

Jika PENGADAAN menolak distribusi yang berstatus `pending`:

1. ✅ Status distribusi diubah menjadi `rejected`
2. ✅ Stok dikembalikan:
   - **Lokasi Asal**: Stok **ditambah** kembali
   - **Lokasi Tujuan**: Stok **dikurangi**
3. ✅ Log ke `stok_histories`:
   - `koreksi_masuk` untuk lokasi asal
   - `koreksi_keluar` untuk lokasi tujuan
4. ✅ Catatan validasi disimpan

## Catatan Penting

- ⚠️ Default status adalah `approved` (auto-approval)
- ⚠️ Metode `validate()` sudah disiapkan untuk future approval workflow
- ⚠️ DOKTER tidak bisa melihat distribusi dari lokasi lain (privacy)
- ⚠️ Route `/distribusi-barang/export` harus di atas route `/distribusi-barang/{id}` untuk menghindari konflik
- ✅ Semua perubahan stok menggunakan DB transaction untuk data consistency

## Testing Checklist

- [ ] DOKTER bisa distribusi barang → Tercatat di log dengan status `approved`
- [ ] DOKTER bisa lihat log distribusi sendiri
- [ ] DOKTER bisa lihat distribusi dari/ke lokasi kliniknya
- [ ] DOKTER **tidak bisa** lihat distribusi lokasi lain
- [ ] PENGADAAN bisa lihat **semua** distribusi
- [ ] Filter tanggal berfungsi
- [ ] Filter lokasi berfungsi
- [ ] Filter barang berfungsi
- [ ] Filter status berfungsi
- [ ] Pagination 20 items per page
- [ ] Detail distribusi accessible
- [ ] Menu "Log Distribusi" muncul di sidebar DOKTER
- [ ] Menu "Log Distribusi" muncul di sidebar PENGADAAN
- [ ] Tombol "Export Excel" hanya muncul untuk PENGADAAN

## Future Enhancement

1. **Excel Export Implementation**
   - Gunakan library `maatwebsite/excel`
   - Export filtered data sesuai kriteria user
   
2. **Approval Workflow** (Jika dibutuhkan)
   - Ubah default status menjadi `pending`
   - DOKTER submit distribusi → status `pending`
   - PENGADAAN approve → status `approved` + stock moved
   - PENGADAAN reject → status `rejected` + no stock movement

3. **Notification System**
   - Notifikasi ke PENGADAAN saat ada distribusi baru
   - Notifikasi ke DOKTER saat distribusi divalidasi

4. **Dashboard Analytics**
   - Chart distribusi per lokasi
   - Chart barang paling sering didistribusikan
   - Alert jika ada pola distribusi tidak normal

---

**Dibuat:** 22 Oktober 2025  
**Status:** ✅ Backend Complete, ✅ Frontend Complete, ⏳ Testing Pending
