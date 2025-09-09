@extends('layouts.pasien-layout')

@section('title', 'Dashboard Pasien')

@section('content')
<div class="container py-4">
    <div class="pt-3 pb-2 mb-3">
        <h1 class="h2">Selamat Datang, {{ $user->nama_karyawan }}!</h1>
        <p class="text-muted">Ini adalah ringkasan kesehatan Anda.</p>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="row">
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total Kunjungan Berobat</div>
                            <div class="text-lg fw-bold">{{ $totalKunjungan }} Kali</div>
                        </div>
                        <i class="bi bi-file-earmark-medical fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-bg-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total Medical Check-up</div>
                            <div class="text-lg fw-bold">{{ $totalCheckup }} Kali</div>
                        </div>
                        <i class="bi bi-clipboard2-pulse fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-bg-secondary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Kunjungan Terakhir</div>
                            <div class="text-lg fw-bold">
                                {{ $kunjunganTerakhir ? \Carbon\Carbon::parse($kunjunganTerakhir->tanggal_kunjungan)->translatedFormat('d M Y') : 'N/A' }}
                            </div>
                        </div>
                        <i class="bi bi-calendar-check fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 mb-4">
            <div class="card text-bg-dark h-100">
                <div class="card-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Check-up Terakhir</div>
                            <div class="text-lg fw-bold">
                                {{ $checkupTerakhir ? \Carbon\Carbon::parse($checkupTerakhir->tanggal_pemeriksaan)->translatedFormat('d M Y') : 'N/A' }}
                            </div>
                        </div>
                        <i class="bi bi-calendar-heart fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL CHECKUP TERAKHIR & TOMBOL AKSI --}}
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-heart-pulse-fill text-danger me-2"></i>
                        Ringkasan Hasil Medical Check-up Terakhir
                    </h5>
                </div>
                <div class="card-body">
                    @if($checkupTerakhir)
                        <div class="row">
                            {{-- ================== PERBAIKAN DI SINI ================== --}}
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between"><span>Tekanan Darah</span> <strong>{{ $checkupTerakhir->tekanan_darah ? $checkupTerakhir->tekanan_darah . ' mmHg' : '-' }}</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Gula Darah</span> <strong>{{ $checkupTerakhir->gula_darah ? $checkupTerakhir->gula_darah . ' mg/dL' : '-' }}</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Kolesterol</span> <strong>{{ $checkupTerakhir->kolesterol ? $checkupTerakhir->kolesterol . ' mg/dL' : '-' }}</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Asam Urat</span> <strong>{{ $checkupTerakhir->asam_urat ? $checkupTerakhir->asam_urat . ' mg/dL' : '-' }}</strong></li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between"><span>Berat Badan</span> <strong>{{ $checkupTerakhir->berat_badan ? $checkupTerakhir->berat_badan . ' Kg' : '-' }}</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Tinggi Badan</span> <strong>{{ $checkupTerakhir->tinggi_badan ? $checkupTerakhir->tinggi_badan . ' cm' : '-' }}</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>IMT</span> <strong>{{ $checkupTerakhir->indeks_massa_tubuh ? $checkupTerakhir->indeks_massa_tubuh . ' kg/m²' : '-' }}</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>Lingkar Perut</span> <strong>{{ $checkupTerakhir->lingkar_perut ? $checkupTerakhir->lingkar_perut . ' cm' : '-' }}</strong></li>
                                </ul>
                            </div>
                            {{-- ======================================================= --}}
                        </div>
                    @else
                        <p class="text-center text-muted p-4">Anda belum memiliki riwayat medical check-up.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
             <div class="card shadow-sm h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-person-vcard display-4 text-primary mb-3"></i>
                    <h5 class="card-title">Lihat Riwayat Lengkap</h5>
                    <p class="card-text">Akses semua riwayat kunjungan dan hasil medical check-up Anda secara detail.</p>
                    <a href="{{ route('pasien.my_card') }}" class="btn btn-primary mt-auto">
                        Buka Kartu Pasien Digital <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection