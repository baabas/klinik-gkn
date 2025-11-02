-- ============================================
-- FIX: Update Tanggal Kunjungan dengan Waktu
-- ============================================
-- Tujuan: Update rekam medis yang waktu-nya 00:00:00 dengan waktu dari created_at
-- Date: 10 Oktober 2025

-- Cek berapa banyak data dengan waktu 00:00:00
SELECT 
    COUNT(*) as total_dengan_waktu_kosong
FROM rekam_medis
WHERE TIME(tanggal_kunjungan) = '00:00:00';

-- Lihat detail data yang akan diupdate
SELECT 
    id_rekam_medis,
    tanggal_kunjungan,
    created_at,
    CASE 
        WHEN created_at IS NOT NULL THEN created_at
        ELSE CONCAT(DATE(tanggal_kunjungan), ' ', CURTIME())
    END as waktu_baru
FROM rekam_medis
WHERE TIME(tanggal_kunjungan) = '00:00:00'
LIMIT 10;

-- UPDATE: Ganti waktu 00:00:00 dengan waktu dari created_at
UPDATE rekam_medis
SET tanggal_kunjungan = created_at
WHERE TIME(tanggal_kunjungan) = '00:00:00'
AND created_at IS NOT NULL;

-- UPDATE: Jika created_at NULL, gunakan waktu sekarang dengan tanggal yang sama
UPDATE rekam_medis
SET tanggal_kunjungan = CONCAT(DATE(tanggal_kunjungan), ' ', CURTIME())
WHERE TIME(tanggal_kunjungan) = '00:00:00'
AND created_at IS NULL;

-- VERIFIKASI: Cek hasilnya
SELECT 
    id_rekam_medis,
    tanggal_kunjungan,
    created_at,
    TIME(tanggal_kunjungan) as waktu,
    CASE 
        WHEN TIME(tanggal_kunjungan) = '00:00:00' THEN '❌ MASIH 00:00'
        ELSE '✅ OK'
    END as status
FROM rekam_medis
ORDER BY id_rekam_medis DESC
LIMIT 20;
