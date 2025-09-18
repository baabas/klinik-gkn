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

    // Rute logout untuk admin (menggunakan POST untuk keamanan)
    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');


    // --- RUTE KHUSUS PASIEN ---
    Route::middleware(['role:PASIEN'])->group(function () {
        Route::get('/kartu-pasien', [PasienController::class, 'myCard'])->name('pasien.my_card');
    });

    // --- RUTE BERSAMA (DOKTER & PENGADAAN) ---
    Route::middleware(['role:DOKTER,PENGADAAN'])->group(function () {
        Route::get('barang-medis/{barang}/riwayat', [BarangMedisController::class, 'history'])
            ->name('barang-medis.history');

        // [BARU] Route untuk proses distribusi stok
        Route::put('barang-medis/{barang}/distribusi', [BarangMedisController::class, 'distribusi'])
            ->name('barang-medis.distribusi');

        Route::get('barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');

        Route::resource('barang-medis', BarangMedisController::class);
        Route::resource('permintaan', PermintaanBarangController::class);
    });

    // --- RUTE KHUSUS DOKTER ---
    Route::middleware(['role:DOKTER'])->group(function () {
        // [BARU] API untuk autocomplete pencarian penyakit
        Route::get('/api/penyakit/{icd10}', [RekamMedisController::class, 'findPenyakit'])->name('api.penyakit.find');

        // Rute untuk menampilkan daftar dan detail pasien
        Route::get('/pasien', [PasienController::class, 'index'])->name('pasien.index');
        Route::get('/pasien/{user:nip}', [PasienController::class, 'show'])->name('pasien.show');

        // Rute untuk mendaftarkan pasien baru
        Route::get('/pasien/create', [PasienController::class, 'create'])->name('pasien.create');
        Route::post('/pasien', [PasienController::class, 'store'])->name('pasien.store');

        // Rute Rekam Medis
        Route::get('/pasien/{user:nip}/rekam-medis/create', [RekamMedisController::class, 'create'])->name('rekam-medis.create');
        Route::post('/pasien/{user:nip}/rekam-medis', [RekamMedisController::class, 'store'])->name('rekam-medis.store');

        // Rute Check-up
        Route::get('/pasien/{user:nip}/checkup/create', [CheckupController::class, 'create'])->name('checkup.create');
        Route::post('/pasien/{user:nip}/checkup', [CheckupController::class, 'store'])->name('checkup.store');

        // Laporan View
        Route::get('/laporan/harian', [LaporanViewController::class, 'laporanHarian'])->name('laporan.harian');
        Route::get('/laporan/pemakaian-obat', [LaporanViewController::class, 'pemakaianObat'])->name('laporan.pemakaian_obat');
        Route::get('/laporan/penyakit', [LaporanViewController::class, 'daftarPenyakit'])->name('laporan.penyakit');
        Route::get('/laporan/kunjungan', [LaporanViewController::class, 'daftarKunjungan'])->name('laporan.kunjungan');

        // Laporan PDF
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/obat', [LaporanController::class, 'cetakLaporanObat'])->name('laporan.obat');
        Route::get('/laporan/penyakit-kunjungan', [LaporanController::class, 'cetakLaporanPenyakitKunjungan'])->name('laporan.penyakit-kunjungan');

        // [BARU] Route untuk dokter mengonfirmasi penerimaan barang
        Route::put('/permintaan/{permintaan}/terima', [PermintaanBarangController::class, 'konfirmasiPenerimaan'])
            ->name('permintaan.terima');
    });

    // --- RUTE KHUSUS PENGADAAN ---
    Route::middleware(['role:PENGADAAN'])->group(function () {
        Route::get('barang-masuk/create', [BarangMasukController::class, 'create'])->name('barang-masuk.create');
        Route::post('barang-masuk', [BarangMasukController::class, 'store'])->name('barang-masuk.store');
    });

});

// Ini adalah rute bawaan Laravel untuk otentikasi pasien (login, register, forgot password)
require __DIR__.'/auth.php';
