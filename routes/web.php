<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanViewController;
use App\Http\Controllers\BarangMedisController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\CheckupController;
use App\Http\Controllers\NonKaryawanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rute default, redirect ke halaman login pasien
Route::get('/', function () {
    return redirect()->route('login');
});

// Route untuk permintaan barang
Route::middleware(['auth'])->group(function () {
    Route::get('/permintaan-barang/create', [PermintaanBarangController::class, 'create'])
        ->name('permintaan-barang.create');
    Route::post('/permintaan-barang/store', [PermintaanBarangController::class, 'store'])
        ->name('permintaan-barang.store');
    Route::get('/permintaan-barang', [PermintaanBarangController::class, 'index'])
        ->name('permintaan-barang.index');
});

// Rute login custom untuk admin (Dokter & Pengadaan)
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.submit');


// Grup rute yang memerlukan otentikasi (login)
Route::middleware(['auth'])->group(function () {

    // --- RUTE UNTUK SEMUA ROLE (PASIEN, DOKTER, PENGADAAN) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');


    // --- RUTE KHUSUS PASIEN ---
    Route::middleware(['role:PASIEN'])->group(function () {
        Route::get('/kartu-pasien', [PasienController::class, 'myCard'])->name('pasien.my_card');
    });

    // --- RUTE BERSAMA (DOKTER & PENGADAAN) ---
    Route::middleware(['role:DOKTER,PENGADAAN'])->group(function () {
        Route::get('barang-medis/{barang}/riwayat', [BarangMedisController::class, 'history'])->name('barang-medis.history');
        Route::put('barang-medis/{barang}/distribusi', [BarangMedisController::class, 'distribusi'])->name('barang-medis.distribusi');
        Route::get('barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
        Route::resource('barang-medis', BarangMedisController::class);
        Route::resource('permintaan', PermintaanBarangController::class);
    });

    // --- RUTE KHUSUS DOKTER ---
    Route::middleware(['role:DOKTER'])->group(function () {

        // Pendaftaran pasien non-karyawan oleh dokter
        Route::get('/pasien-non-karyawan/create', [NonKaryawanController::class, 'create'])->name('non_karyawan.create');
        Route::post('/pasien-non-karyawan', [NonKaryawanController::class, 'store'])->name('non_karyawan.store');

        Route::get('/api/penyakit/{icd10}', [RekamMedisController::class, 'findPenyakit'])->name('api.penyakit.find');

        // --- PERUBAHAN: Rute Pasien, Rekam Medis, dan Checkup Disatukan ---
        
        // Daftar Pasien (tetap sama)
        Route::get('/pasien', [PasienController::class, 'index'])->name('pasien.index');

        // Detail Pasien (menggunakan {identifier} generik)
        Route::get('/pasien/{identifier}', [PasienController::class, 'show'])->name('pasien.show');

        // Membuat Rekam Medis
        Route::get('/pasien/{identifier}/rekam-medis/create', [RekamMedisController::class, 'create'])->name('rekam-medis.create');
        Route::post('/pasien/{identifier}/rekam-medis', [RekamMedisController::class, 'store'])->name('rekam-medis.store');

        // Membuat Check-up
        Route::get('/pasien/{identifier}/checkup/create', [CheckupController::class, 'create'])->name('checkup.create');
        Route::post('/pasien/{identifier}/checkup', [CheckupController::class, 'store'])->name('checkup.store');
        
        // --- AKHIR PERUBAHAN ---

        // Laporan
        Route::get('/laporan/harian', [LaporanViewController::class, 'laporanHarian'])->name('laporan.harian');
        Route::get('/laporan/pemakaian-obat', [LaporanViewController::class, 'pemakaianObat'])->name('laporan.pemakaian_obat');
        Route::get('/laporan/penyakit', [LaporanViewController::class, 'daftarPenyakit'])->name('laporan.penyakit');
        Route::get('/laporan/kunjungan', [LaporanViewController::class, 'daftarKunjungan'])->name('laporan.kunjungan');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/obat', [LaporanController::class, 'cetakLaporanObat'])->name('laporan.obat');
        Route::get('/laporan/penyakit-kunjungan', [LaporanController::class, 'cetakLaporanPenyakitKunjungan'])->name('laporan.penyakit-kunjungan');

        Route::put('/permintaan/{permintaan}/terima', [PermintaanBarangController::class, 'konfirmasiPenerimaan'])->name('permintaan.terima');
    });

    // --- RUTE KHUSUS PENGADAAN ---
    Route::middleware(['role:PENGADAAN'])->group(function () {
        Route::get('barang-masuk/create', [BarangMasukController::class, 'create'])->name('barang-masuk.create');
        Route::post('barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
    });

});

// Rute bawaan Laravel untuk otentikasi pasien
require __DIR__.'/auth.php';