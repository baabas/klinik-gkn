<?php

namespace App\Helpers;

use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use App\Models\PendingStokMasuk;
use App\Models\BarangMedis;
use Illuminate\Support\Facades\Auth;

class PengadaanNotificationHelper
{
    /**
     * Hitung jumlah permintaan dengan status PENDING
     */
    public static function countPendingRequests()
    {
        return PermintaanBarang::where('status', 'PENDING')->count();
    }

    /**
     * Hitung jumlah barang baru yang perlu ditambahkan ke master data
     * (item yang disetujui tetapi barang belum ada di master data)
     */
    public static function countNewItemsToAdd()
    {
        return DetailPermintaanBarang::whereHas('permintaan', function($query) {
            $query->where('status', 'APPROVED');
        })
        ->whereNull('id_barang') // Barang belum ada di master data
        ->count();
    }

    /**
     * Hitung jumlah permintaan yang sudah disetujui dan siap untuk input barang masuk
     * Hanya hitung permintaan dengan status APPROVED (belum diproses sama sekali)
     * Permintaan dengan status PROCESSING sudah tidak perlu notifikasi karena sudah diinput
     */
    public static function countApprovedRequestsForInput()
    {
        // Hitung permintaan yang disetujui dan belum diproses (status APPROVED)
        // Tidak termasuk yang sudah status PROCESSING karena pengadaan sudah input barang masuk
        return PermintaanBarang::where('status', 'APPROVED')->count();
    }

    /**
     * Hitung jumlah barang dengan stok kritis (di bawah atau sama dengan stok minimal)
     * Menggunakan satuan terkecil untuk perbandingan yang lebih akurat
     */
    public static function countCriticalStock()
    {
        return BarangMedis::withSum('stok as stok_sum_jumlah', 'jumlah')
            ->get()
            ->filter(function ($barang) {
                $totalStok = (int)($barang->stok_sum_jumlah ?? 0);
                return $barang->isStokKritis($totalStok);
            })
            ->count();
    }

    /**
     * Hitung total semua notifikasi pengadaan
     */
    public static function countTotalNotifications()
    {
        return self::countPendingRequests() + 
               self::countNewItemsToAdd() + 
               self::countApprovedRequestsForInput() +
               self::countCriticalStock();
    }

    /**
     * Dapatkan semua data notifikasi dalam satu array
     */
    public static function getAllNotifications()
    {
        return [
            'pending_requests' => self::countPendingRequests(),
            'new_items_to_add' => self::countNewItemsToAdd(),
            'approved_for_input' => self::countApprovedRequestsForInput(),
            'critical_stock' => self::countCriticalStock(),
            'total' => self::countTotalNotifications(),
        ];
    }

    /**
     * Cek apakah user adalah pengadaan
     */
    public static function isPengadaanUser()
    {
        return Auth::check() && Auth::user()->akses === 'PENGADAAN';
    }
}