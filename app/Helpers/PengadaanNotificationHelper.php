<?php

namespace App\Helpers;

use App\Models\PermintaanBarang;
use App\Models\DetailPermintaanBarang;
use App\Models\PendingStokMasuk;
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
     * (berdasarkan PendingStokMasuk atau permintaan dengan status APPROVED)
     */
    public static function countApprovedRequestsForInput()
    {
        // Hitung pending stock masuk yang belum diproses
        $pendingStock = PendingStokMasuk::count();
        
        // Jika tidak ada pending stock, hitung permintaan yang disetujui tapi belum diproses
        if ($pendingStock == 0) {
            $approvedRequests = PermintaanBarang::where('status', 'APPROVED')->count();
            return $approvedRequests;
        }
        
        return $pendingStock;
    }

    /**
     * Hitung total semua notifikasi pengadaan
     */
    public static function countTotalNotifications()
    {
        return self::countPendingRequests() + 
               self::countNewItemsToAdd() + 
               self::countApprovedRequestsForInput();
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