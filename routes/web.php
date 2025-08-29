<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\RekamMedisController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanViewController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rute login untuk admin
Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'store']);

Route::middleware(['auth'])->group(function () {
    // Rute Pasien
    Route::get('/kartu-pasien', [PasienController::class, 'myCard'])->name('pasien.my_card');

    // Rute yang hanya bisa diakses DOKTER
    Route::middleware(['role:DOKTER'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/pasien', [PasienController::class, 'index'])->name('pasien.index');
        Route::get('/pasien/{user:nip}', [PasienController::class, 'show'])->name('pasien.show');
        Route::get('/pasien/{user:nip}/rekam-medis/create', [RekamMedisController::class, 'create'])->name('rekam-medis.create');

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');


        Route::get('/laporan/obat', [LaporanController::class, 'cetakLaporanObat'])->name('laporan.obat');


        Route::get('/laporan/penyakit-kunjungan', [LaporanController::class, 'cetakLaporanPenyakitKunjungan'])->name('laporan.penyakit-kunjungan');
        Route::post('/pasien/{user:nip}/rekam-medis', [RekamMedisController::class, 'store'])->name('rekam-medis.store');

        Route::get('/laporan/pemakaian-obat', [LaporanViewController::class, 'pemakaianObat'])->name('laporan.pemakaian_obat');

        Route::get('/laporan/penyakit', [LaporanViewController::class, 'daftarPenyakit'])->name('laporan.penyakit');
        Route::get('/laporan/pemakaian-obat', [LaporanViewController::class, 'pemakaianObat'])->name('laporan.pemakaian_obat');
        Route::get('/laporan/kunjungan', [LaporanViewController::class, 'daftarKunjungan'])->name('laporan.kunjungan');


        Route::resource('obat', ObatController::class);

        Route::get('/laporan/harian', [LaporanViewController::class, 'laporanHarian'])->name('laporan.harian');

        Route::resource('obat', ObatController::class);

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/obat', [LaporanController::class, 'cetakLaporanObat'])->name('laporan.obat');
    });

    // Rute profil untuk semua user yang login
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
