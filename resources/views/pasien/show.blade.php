@extends('layouts.sidebar-layout')

@section('title', 'Detail Pasien - ' . $user->nama_karyawan)

@push('styles')
<style>
    .table td {
        vertical-align: middle;
    }
    .history-card .nav-link {
        font-weight: 500;
    }
    .history-card .nav-link.active {
        border-bottom-width: 3px;
    }
    .list-diagnosa, .list-resep {
        padding-left: 1.2rem;
        margin-bottom: 0.5rem;
    }
    .list-diagnosa li, .list-resep li {
        padding-bottom: 0.25rem;
    }
    .section-title {
        font-weight: 600;
        color: var(--bs-secondary-emphasis);
        display: block;
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Kartu Pasien Digital</h1>
</div>

{{-- BIODATA KARTU (TELAH DIMODIFIKASI) --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Nomor Index Pasien: {{ $user->id }}</h4>
    </div>
    <div class="card-body p-4">
        @if($karyawan)
            <div class="row">
                <div class="col-md-6">
                    <p><strong>NIP:</strong><br> {{ $user->nip }}</p>
                    <p><strong>Nama:</strong><br> {{ $user->nama_karyawan }}</p>
                    <p><strong>Tanggal Lahir:</strong><br>
                        {{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                    </p>
                    <p class="mb-md-0"><strong>Usia:</strong><br>
                        {{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->age . ' Tahun' : '-' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Kantor:</strong><br> {{ $karyawan->kantor ?? '-' }}</p>
                    <p class="mb-0"><strong>Alamat:</strong><br> {{ $karyawan->alamat ?? '-' }}</p>
                </div>
            </div>
        @else
            <p class="text-center text-danger">Data detail karyawan tidak ditemukan.</p>
        @endif
    </div>
</div>

{{-- TABEL RIWAYAT (DENGAN UX YANG LEBIH BAIK) --}}
<div class="card shadow-sm history-card">
    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center">
        <ul class="nav nav-tabs card-header-tabs" id="riwayatTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="kunjungan-tab" data-bs-toggle="tab" data-bs-target="#kunjungan-tab-pane" type="button" role="tab">
                    <i class="bi bi-file-earmark-medical me-1"></i> Riwayat Kunjungan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="checkup-tab" data-bs-toggle="tab" data-bs-target="#checkup-tab-pane" type="button" role="tab">
                    <i class="bi bi-heart-pulse me-1"></i> Riwayat Check-up
                </button>
            </li>
        </ul>
        <div class="mt-2 mt-md-0">
            @if(Auth::user()->hasRole('DOKTER'))
                <div class="btn-group">
                     <a href="{{ route('rekam-medis.create', $user->nip) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i> Rekam Medis Baru
                    </a>
                    <a href="{{ route('checkup.create', $user->nip) }}" class="btn btn-info btn-sm text-white">
                        <i class="bi bi-clipboard2-pulse"></i> Check-up Baru
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="tab-content" id="riwayatTabContent">
            {{-- KONTEN TAB 1: RIWAYAT KUNJUNGAN --}}
            <div class="tab-pane fade show active" id="kunjungan-tab-pane" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr><th>Tanggal</th><th>Pemeriksaan</th><th>Terapi</th><th>Berobat Untuk</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($user->rekamMedis as $rekam)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($rekam->tanggal_kunjungan)->translatedFormat('d M Y, H:i') }}</td>
                                    <td>
                                        @if($rekam->detailDiagnosa->isNotEmpty())
                                            <span class="section-title">Diagnosa:</span>
                                            <ul class="list-unstyled list-diagnosa">
                                                @foreach($rekam->detailDiagnosa as $diagnosa)
                                                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $diagnosa->penyakit->nama_penyakit ?? 'N/A' }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        @if($rekam->riwayat_sakit)
                                            <span class="section-title">Keluhan:</span> {{ $rekam->riwayat_sakit }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($rekam->resepObat->isNotEmpty())
                                            <span class="section-title">Resep Obat:</span>
                                            <ul class="list-unstyled list-resep">
                                                @foreach($rekam->resepObat as $resep)
                                                    <li><i class="bi bi-prescription me-2"></i>{{ $resep->obat->nama_obat ?? 'N/A' }} <span class="badge bg-secondary rounded-pill">{{ $resep->jumlah }}</span></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                         @if($rekam->pengobatan)
                                            <span class="section-title">Catatan Terapi:</span> {{ $rekam->pengobatan }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($rekam->nama_sa)
                                            <strong>{{ $rekam->nama_sa }}</strong> <br><small>({{ $rekam->jenis_kelamin_sa }})</small>
                                        @else
                                            <span class="badge bg-light text-dark">Diri Sendiri</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center p-4">Belum ada riwayat kunjungan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- KONTEN TAB 2: RIWAYAT CHECK-UP --}}
            <div class="tab-pane fade" id="checkup-tab-pane" role="tabpanel">
                <div class="table-responsive">
                     <table class="table table-hover">
                        <thead class="table-light">
                            <tr><th>Tgl Pemeriksaan</th><th>Hasil Pemeriksaan</th><th>Hasil Pengukuran</th><th>Diperiksa Untuk</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($user->checkups as $checkup)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($checkup->tanggal_pemeriksaan)->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            <li>Tekanan Darah: <strong>{{ $checkup->tekanan_darah ? $checkup->tekanan_darah . ' mmHg' : '-' }}</strong></li>
                                            <li>Gula Darah: <strong>{{ $checkup->gula_darah ? $checkup->gula_darah . ' mg/dL' : '-' }}</strong></li>
                                            <li>Kolesterol: <strong>{{ $checkup->kolesterol ? $checkup->kolesterol . ' mg/dL' : '-' }}</strong></li>
                                            <li>Asam Urat: <strong>{{ $checkup->asam_urat ? $checkup->asam_urat . ' mg/dL' : '-' }}</strong></li>
                                        </ul>
                                    </td>
                                    <td>
                                         <ul class="list-unstyled mb-0 small">
                                            <li>Berat Badan: <strong>{{ $checkup->berat_badan ? $checkup->berat_badan . ' Kg' : '-' }}</strong></li>
                                            <li>Tinggi Badan: <strong>{{ $checkup->tinggi_badan ? $checkup->tinggi_badan . ' cm' : '-' }}</strong></li>
                                            <li>IMT: <strong>{{ $checkup->indeks_massa_tubuh ? $checkup->indeks_massa_tubuh . ' kg/m²' : '-' }}</strong></li>
                                            <li>Lingkar Perut: <strong>{{ $checkup->lingkar_perut ? $checkup->lingkar_perut . ' cm' : '-' }}</strong></li>
                                        </ul>
                                    </td>
                                    <td>
                                        @if($checkup->nama_sa)
                                            <strong>{{ $checkup->nama_sa }}</strong> <br><small>({{ $checkup->jenis_kelamin_sa }})</small>
                                        @else
                                            <span class="badge bg-light text-dark">Diri Sendiri</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center p-4">Belum ada riwayat check-up.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
