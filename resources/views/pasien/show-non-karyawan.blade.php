@extends('layouts.sidebar-layout')

@section('title', 'Detail Pasien - ' . $pasien->nama_karyawan)

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

{{-- BIODATA KARTU (disesuaikan untuk Non-Karyawan) --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Nomor Index Pasien: {{ $pasien->id }}</h4>
        @if(Auth::user()->hasRole('DOKTER'))
            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#editInfoModal">
                <i class="bi bi-pencil"></i> Edit Info Pasien
            </button>
        @endif
    </div>
    <div class="card-body p-4">
        @if($pasien->nonKaryawan)
            <div class="row">
                <div class="col-md-4">
                    <p><strong>NIK:</strong><br> {{ $pasien->nik }}</p>
                    <p><strong>Nama:</strong><br> {{ $pasien->nama_karyawan }}</p>
                    <p><strong>Tanggal Lahir:</strong><br>
                        {{ $pasien->nonKaryawan->tanggal_lahir ? \Carbon\Carbon::parse($pasien->nonKaryawan->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                    </p>
                    <p class="mb-md-0"><strong>Alamat:</strong><br> {{ $pasien->nonKaryawan->alamat ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <p><strong>Usia:</strong><br>
                        {{ $pasien->nonKaryawan->tanggal_lahir ? \Carbon\Carbon::parse($pasien->nonKaryawan->tanggal_lahir)->age . ' Tahun' : '-' }}
                    </p>
                    <p><strong>Jenis Kelamin:</strong><br>
                        {{ $pasien->nonKaryawan->jenis_kelamin == 'L' ? 'Laki-laki' : ($pasien->nonKaryawan->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                    </p>
                    <p><strong>No. HP:</strong><br> {{ $pasien->nonKaryawan->no_hp ?? '-' }}</p>
                    <p class="mb-md-0"><strong>Lokasi Gedung:</strong><br> {{ $pasien->nonKaryawan->lokasi_gedung ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    @if($pasien->nonKaryawan->alergi)
                        <div class="alert alert-warning mb-0" role="alert">
                            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Alergi:</strong><br>
                            {{ $pasien->nonKaryawan->alergi }}
                        </div>
                    @endif
                </div>
            </div>
        @else
            <p class="text-center text-danger">Data detail pasien tidak ditemukan.</p>
        @endif
    </div>
</div>

{{-- TABEL RIWAYAT --}}
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
                    <a href="{{ route('rekam-medis.create.non_karyawan', $pasien->nik) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle"></i> Rekam Medis Baru
                    </a>
                    <a href="{{ route('checkup.create.non_karyawan', $pasien->nik) }}" class="btn btn-info btn-sm text-white">
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
                            <tr><th>Tanggal</th><th>Anamnesa</th><th>Treatment</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($pasien->rekamMedisNonKaryawan as $rekam)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($rekam->tanggal_kunjungan)->translatedFormat('d M Y') }}</td>
                                    <td>
                                        @if($rekam->anamnesa)
                                            <span class="section-title">Keluhan:</span> {{ $rekam->anamnesa }}
                                        @endif
                                        @if($rekam->detailDiagnosa->isNotEmpty())
                                            <span class="section-title">Diagnosa:</span>
                                            <ul class="list-unstyled list-diagnosa">
                                                @foreach($rekam->detailDiagnosa as $diagnosa)
                                                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>{{ $diagnosa->penyakit->nama_penyakit ?? 'N/A' }}</li>
                                                @endforeach
                                            </ul>
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
                                         @if($rekam->treatment)
                                            <span class="section-title">Advice:</span> {{ $rekam->treatment }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center p-4">Belum ada riwayat kunjungan.</td></tr>
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
                            <tr><th>Tgl Pemeriksaan</th><th>Hasil Pemeriksaan</th><th>Hasil Pengukuran</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($pasien->checkupNonKaryawan as $checkup)
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
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center p-4">Belum ada riwayat check-up.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Info Pasien Non-Karyawan --}}
@if(Auth::user()->hasRole('DOKTER') && $pasien->nonKaryawan)
<div class="modal fade" id="editInfoModal" tabindex="-1" aria-labelledby="editInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('pasien.update_non_karyawan_info', $pasien->nik) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="editInfoModalLabel">Edit Informasi Pasien</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Hanya field berikut yang dapat diupdate: Alergi, No. HP, Lokasi Gedung, dan Alamat.</small>
                    </div>

                    <div class="mb-3">
                        <label for="alergi" class="form-label">Alergi</label>
                        <textarea name="alergi" id="alergi" class="form-control @error('alergi') is-invalid @enderror" rows="2" placeholder="Masukkan informasi alergi pasien">{{ old('alergi', $pasien->nonKaryawan->alergi) }}</textarea>
                        @error('alergi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Contoh: Alergi obat X, makanan Y, dll.</small>
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $pasien->nonKaryawan->no_hp) }}" placeholder="Contoh: 081234567890">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lokasi_gedung" class="form-label">Lokasi Gedung</label>
                        <select name="lokasi_gedung" id="lokasi_gedung" class="form-select @error('lokasi_gedung') is-invalid @enderror">
                            <option value="">Pilih Lokasi Gedung</option>
                            <option value="GKN 1" {{ old('lokasi_gedung', $pasien->nonKaryawan->lokasi_gedung) == 'GKN 1' ? 'selected' : '' }}>GKN 1</option>
                            <option value="GKN 2" {{ old('lokasi_gedung', $pasien->nonKaryawan->lokasi_gedung) == 'GKN 2' ? 'selected' : '' }}>GKN 2</option>
                        </select>
                        @error('lokasi_gedung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Masukkan alamat lengkap pasien">{{ old('alamat', $pasien->nonKaryawan->alamat) }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection