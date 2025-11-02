-- ============================================
-- FIX: Update Karyawan dari Users
-- ============================================
-- Tujuan: Agar nama pasien & dokter tampil di struk, bukan NIP
-- Date: 09 Oktober 2025
-- PENTING: Kolom di tabel users DAN karyawan keduanya adalah 'nama_karyawan' (BUKAN 'name' atau 'nama')

-- ============================================
-- STEP 1: CEK DATA YANG BERMASALAH
-- ============================================

-- Cek karyawan dengan nama kosong/NULL
SELECT 
    k.nip,
    k.nama_karyawan as nama_karyawan,
    u.nama_karyawan as nama_user
FROM karyawan k
LEFT JOIN users u ON k.nip = u.nip
WHERE k.nama_karyawan IS NULL OR k.nama_karyawan = '' OR TRIM(k.nama_karyawan) = '';

-- Cek users yang belum ada di tabel karyawan
SELECT 
    u.id,
    u.nip,
    u.nama_karyawan,
    u.email,
    'NOT IN KARYAWAN' as status
FROM users u
WHERE u.nip IS NOT NULL
AND u.nip NOT IN (SELECT nip FROM karyawan WHERE nip IS NOT NULL);

-- ============================================
-- STEP 2: UPDATE KARYAWAN YANG NAMA KOSONG
-- ============================================

-- 1. UPDATE: Sync nama dari users ke karyawan yang kosong
UPDATE karyawan k
INNER JOIN users u ON k.nip = u.nip
SET k.nama_karyawan = u.nama_karyawan
WHERE (k.nama_karyawan IS NULL OR k.nama_karyawan = '' OR TRIM(k.nama_karyawan) = '')
AND u.nama_karyawan IS NOT NULL
AND TRIM(u.nama_karyawan) != '';

-- 2. INSERT: Tambahkan user yang belum ada di karyawan
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

-- ============================================
-- STEP 3: VERIFIKASI HASIL
-- ============================================

-- VERIFIKASI: Cek hasilnya
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

-- Hitung berapa yang berhasil dan gagal
SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN k.nama_karyawan IS NOT NULL AND k.nama_karyawan != '' THEN 1 ELSE 0 END) as sukses,
    SUM(CASE WHEN k.nama_karyawan IS NULL OR k.nama_karyawan = '' THEN 1 ELSE 0 END) as masih_kosong
FROM users u
LEFT JOIN karyawan k ON u.nip = k.nip
WHERE u.nip IS NOT NULL;

-- ============================================
-- MANUAL FIX (Jika Masih Ada yang Kosong)
-- ============================================

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
