<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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
use App\Http\Controllers\DaftarPenyakitController;
use App\Http\Controllers\FeedbackController; // [BARU] Import FeedbackController
use App\Http\Controllers\MasterKantorController; // [BARU] Import MasterKantorController
use App\Http\Controllers\MasterIsiKemasanController; // [BARU] Import MasterIsiKemasanController
use App\Http\Controllers\MasterSatuanController; // [BARU] Import MasterSatuanController
use App\Http\Controllers\DistribusiBarangController; // [BARU] Import DistribusiBarangController

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
    Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

    // Test route untuk debugging
    Route::get('/test-dashboard', function() {
        return '<h1>Test route berhasil!</h1><p>Waktu: ' . now() . '</p>';
    });

    Route::get('/test-auth', function() {
        $user = Auth::user();
        $activeRole = session('active_role');
        return response()->json([
            'user' => $user ? $user->only(['id', 'name', 'email']) : null,
            'active_role' => $activeRole,
            'user_roles' => $user ? $user->roles->pluck('name') : [],
            'session_data' => session()->all()
        ]);
    });

    Route::get('/test-simple', function() {
        return view('dashboard-simple', [
            'kasus_hari_ini' => 5,
            'data_penyakit' => collect([
                (object)['nama_penyakit' => 'Flu', 'jumlah' => 3],
                (object)['nama_penyakit' => 'Demam', 'jumlah' => 2]
            ]),
            'data_obat' => collect([
                (object)['nama_obat' => 'Paracetamol', 'jumlah' => 10]
            ]),
            'total_kasus_penyakit' => 5,
            'total_pemakaian_obat' => 10
        ]);
    });


    // --- RUTE KHUSUS PASIEN ---
    Route::middleware(['role:PASIEN'])->group(function () {
        Route::get('/kartu-pasien', [PasienController::class, 'myCard'])->name('pasien.my_card');
    });

    // --- RUTE BERSAMA (DOKTER & PENGADAAN) ---
    Route::middleware(['role:DOKTER,PENGADAAN'])->group(function () {
        Route::get('barang-medis/{barang}/riwayat', [BarangMedisController::class, 'history'])->name('barang-medis.history');
        Route::put('barang-medis/{barang}/distribusi', [BarangMedisController::class, 'distribusi'])->name('barang-medis.distribusi');
        Route::get('barang-masuk', [BarangMasukController::class, 'index'])->name('barang-masuk.index');
        Route::get('/api/barang-medis/search', [BarangMedisController::class, 'search'])->name('api.barang-medis.search');
        Route::get('/barang-medis/print-pdf', [BarangMedisController::class, 'printPdf'])
            ->name('barang-medis.printPdf')
            ->middleware('role:PENGADAAN');
        Route::resource('barang-medis', BarangMedisController::class);
        Route::resource('permintaan', PermintaanBarangController::class);
        Route::get('/permintaan/{permintaan}/print-pdf', [PermintaanBarangController::class, 'printPdf'])->name('permintaan.print-pdf');

        // Rute untuk Laporan
    });

    // --- RUTE KHUSUS DOKTER ---
    Route::middleware(['role:DOKTER'])->group(function () {

        // Pendaftaran pasien non-karyawan oleh dokter
        Route::get('/pasien-non-karyawan/create', [NonKaryawanController::class, 'create'])->name('non_karyawan.create');
        Route::post('/pasien-non-karyawan', [NonKaryawanController::class, 'store'])->name('non_karyawan.store');

        // Daftar Penyakit CRUD
        Route::resource('daftar-penyakit', DaftarPenyakitController::class);
        Route::get('/api/daftar-penyakit/search', [DaftarPenyakitController::class, 'search'])->name('api.daftar-penyakit.search');

        Route::get('/api/penyakit/{icd10}', [RekamMedisController::class, 'findPenyakit'])->name('api.penyakit.find');
        Route::get('/api/penyakit-search', [RekamMedisController::class, 'searchPenyakit'])->name('api.penyakit.search');
        Route::get('/api/obat-search', [RekamMedisController::class, 'searchObat'])->name('api.obat.search');
        Route::get('/api/pasien-search', [PasienController::class, 'searchPasien'])->name('api.pasien.search');

        // Daftar dan Detail Pasien (parameter disamakan menjadi 'pasien')
        Route::get('/pasien', [PasienController::class, 'index'])->name('pasien.index');
        Route::get('/pasien/{pasien:nip}', [PasienController::class, 'show'])->name('pasien.show');
        Route::get('/pasien-non-karyawan/{pasien:nik}', [PasienController::class, 'showNonKaryawan'])->name('pasien.show_non_karyawan');

        // Rekam Medis (parameter disamakan menjadi 'pasien')
        Route::get('/pasien/{pasien:nip}/rekam-medis/create', [RekamMedisController::class, 'create'])->name('rekam-medis.create');
        Route::post('/pasien/{pasien:nip}/rekam-medis', [RekamMedisController::class, 'store'])->name('rekam-medis.store');
        Route::get('/pasien-non-karyawan/{pasien:nik}/rekam-medis/create', [RekamMedisController::class, 'create'])->name('rekam-medis.create.non_karyawan');
        Route::post('/pasien-non-karyawan/{pasien:nik}/rekam-medis', [RekamMedisController::class, 'store'])->name('rekam-medis.store.non_karyawan');
        
        // [BARU] Print Resep Obat
        Route::get('/rekam-medis/{id}/print-resep', [RekamMedisController::class, 'printResep'])->name('rekam-medis.print-resep');

        // Check-up (parameter disamakan menjadi 'pasien')
        Route::get('/pasien/{pasien:nip}/checkup/create', [CheckupController::class, 'create'])->name('checkup.create');
        Route::post('/pasien/{pasien:nip}/checkup', [CheckupController::class, 'store'])->name('checkup.store');
        Route::get('/pasien-non-karyawan/{pasien:nik}/checkup/create', [CheckupController::class, 'create'])->name('checkup.create.non_karyawan');
        Route::post('/pasien-non-karyawan/{pasien:nik}/checkup', [CheckupController::class, 'store'])->name('checkup.store.non_karyawan');

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
        Route::post('barang-masuk/store-multiple', [BarangMasukController::class, 'storeMultiple'])->name('barang-masuk.store-multiple');
        Route::get('barang-masuk/check-completion/{requestId}', [BarangMasukController::class, 'checkCompletion'])->name('barang-masuk.check-completion');
        Route::get('/barang-medis/print-pdf', [BarangMedisController::class, 'printPdf'])->name('barang-medis.printPdf');
        
        // [BARU] Laporan Feedback untuk Pengadaan
        Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
        
        // [BARU] Master Data (Role: PENGADAAN)
        Route::resource('master-kantor', MasterKantorController::class);
        Route::resource('master-isi-kemasan', MasterIsiKemasanController::class);
        Route::resource('master-satuan', MasterSatuanController::class);
        
        // Log Distribusi Barang - Read Only untuk Audit Trail (PENGADAAN Only)
        Route::get('/distribusi-barang', [DistribusiBarangController::class, 'index'])->name('distribusi-barang.index');
        Route::get('/distribusi-barang/{id}', [DistribusiBarangController::class, 'show'])->name('distribusi-barang.show');
    });

});

// [BARU] Rute Feedback Pasien (Tablet Perawat - Tanpa Auth)
Route::get('/feedback/form', [FeedbackController::class, 'showFeedbackForm'])->name('feedback.form');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::get('/api/feedback/check-pending', [FeedbackController::class, 'checkPendingFeedback'])->name('api.feedback.check-pending');


// Rute bawaan Laravel untuk otentikasi pasien
require __DIR__.'/auth.php';
