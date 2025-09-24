@extends('layouts.sidebar-layout')

@section('title', 'Buat Rekam Medis - ' . $user->nama_karyawan)

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .card-header { background-color: #f8f9fa; }
        .form-section-title { font-weight: 600; color: var(--bs-primary); }
    </style>
@endpush

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Rekam Medis Baru</h1>
</div>

@php
    $actionRoute = $user->nip 
        ? route('rekam-medis.store', $user->nip) 
        : route('rekam-medis.store.non_karyawan', $user->nik);
@endphp

<form action="{{ $actionRoute }}" method="POST" id="rekamMedisForm">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            {{-- Bagian Utama Form --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0 form-section-title">1. Anamnesa (Pemeriksaan Subjektif)</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_kunjungan" class="form-label fw-bold">Tanggal Kunjungan <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('tanggal_kunjungan') is-invalid @enderror" id="tanggal_kunjungan" name="tanggal_kunjungan" value="{{ old('tanggal_kunjungan', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_kunjungan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="anamnesa" class="form-label fw-bold">Keluhan & Riwayat Sakit</label>
                        <textarea class="form-control" id="anamnesa" name="anamnesa" rows="4" placeholder="Contoh: Pasien datang dengan keluhan demam selama 3 hari, batuk, dan sakit tenggorokan.">{{ old('anamnesa') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0 form-section-title">2. Diagnosa</h5></div>
                <div class="card-body">
                    <label class="form-label fw-bold">Diagnosa Penyakit</label>
                    <div id="diagnosa-container">
                        <div class="row g-2 mb-2 align-items-center diagnosa-entry">
                            <div class="col-sm-3">
                                <input type="text" name="diagnosa[0][kode_penyakit]" class="form-control icd10-input" placeholder="Ketik Kode ICD-10">
                            </div>
                            <div class="col-sm-8">
                                <input type="text" class="form-control nama-penyakit-output" placeholder="Nama Penyakit (otomatis)" readonly style="background-color: #e9ecef;">
                            </div>
                            <div class="col-sm-1 text-end">
                                </div>
                        </div>
                    </div>
                    <button type="button" id="add-diagnosa" class="btn btn-sm btn-outline-success mt-2">
                        <i class="bi bi-plus-circle"></i> Tambah Diagnosa
                    </button>
                    @error('diagnosa.*.kode_penyakit') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0 form-section-title">3. Resep Obat & Terapi</h5></div>
                <div class="card-body">
                    <label class="form-label fw-bold">Resep Obat</label>
                    <div id="resep-obat-container">
                        {{-- Akan diisi oleh JavaScript --}}
                    </div>
                    <button type="button" id="add-resep" class="btn btn-sm btn-outline-primary mt-2"><i class="bi bi-plus-circle"></i> Tambah Obat</button>
                    <hr class="my-3">
                    <div class="mb-3">
                        <label for="terapi" class="form-label fw-bold">Catatan Pengobatan / Lainnya</label>
                        <textarea class="form-control" id="terapi" name="terapi" rows="3" placeholder="Contoh: Istirahat yang cukup, perbanyak minum air putih.">{{ old('terapi') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Bagian Samping --}}
            <div class="card shadow-sm mb-4">
                 <div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Informasi Pasien</h5><i class="bi bi-person-circle fs-4 text-primary"></i></div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">{{ $user->nama_karyawan }}</h5>
                    <p class="card-text text-muted mb-0">
                        @if($user->nip)
                            NIP: {{ $user->nip }}
                        @elseif($user->nik)
                            NIK: {{ $user->nik }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0">Keterangan Tambahan</h5></div>
                <div class="card-body">
                    <p class="form-text mt-0 mb-2">Isi jika berobat untuk keluarga.</p>
                    <div class="mb-3">
                        <label for="nama_sa" class="form-label">Nama Suami / Istri / Anak</label>
                        <input type="text" id="nama_sa" name="nama_sa" class="form-control" value="{{ old('nama_sa') }}" placeholder="Opsional">
                    </div>
                    <div>
                        <label for="jenis_kelamin_sa" class="form-label">Jenis Kelamin</label>
                        <select id="jenis_kelamin_sa" name="jenis_kelamin_sa" class="form-select">
                            <option value="" selected>Pilih...</option>
                            <option value="Laki-laki" {{ old('jenis_kelamin_sa') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ old('jenis_kelamin_sa') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                 <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save"></i> Simpan Rekam Medis</button>
                 @php
                    $cancelRoute = $user->nip 
                        ? route('pasien.show', $user->nip) 
                        : route('pasien.show_non_karyawan', $user->nik);
                 @endphp
                <a href="{{ $cancelRoute }}" class="btn btn-outline-secondary">Batal</a>
            </div>
             @if(session('error'))<div class="alert alert-danger mt-3" role="alert">{{ session('error') }}</div>@endif
        </div>
    </div>
</form>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // --- FUNGSI AUTOCOMPLETE ICD-10 ---
            $('#diagnosa-container').on('keyup', '.icd10-input', function() {
                let icd10Input = $(this);
                let namaPenyakitOutput = icd10Input.closest('.diagnosa-entry').find('.nama-penyakit-output');
                let query = icd10Input.val();

                if (query.length >= 2) {
                    $.ajax({
                        url: `{{ url('/api/penyakit') }}/${query}`,
                        type: 'GET',
                        success: function(data) {
                            if (data.success) {
                                namaPenyakitOutput.val(data.nama_penyakit);
                                icd10Input.val(data.kode_penyakit);
                            } else {
                                namaPenyakitOutput.val('Kode tidak ditemukan');
                            }
                        },
                        error: function() {
                            namaPenyakitOutput.val('Gagal memuat data');
                        }
                    });
                } else {
                    namaPenyakitOutput.val('');
                }
            });

            // --- FUNGSI TAMBAH DIAGNOSA ---
            let diagnosaIndex = 1;
            $('#add-diagnosa').on('click', function() {
                const newDiagnosa = `
                    <div class="row g-2 mb-2 align-items-center diagnosa-entry">
                        <div class="col-sm-3">
                            <input type="text" name="diagnosa[${diagnosaIndex}][kode_penyakit]" class="form-control icd10-input" placeholder="Ketik Kode ICD-10">
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control nama-penyakit-output" placeholder="Nama Penyakit (otomatis)" readonly style="background-color: #e9ecef;">
                        </div>
                        <div class="col-sm-1 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-diagnosa"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>`;
                $('#diagnosa-container').append(newDiagnosa);
                diagnosaIndex++;
            });
            $('#diagnosa-container').on('click', '.remove-diagnosa', function() {
                $(this).closest('.diagnosa-entry').remove();
            });

            // --- FUNGSI TAMBAH RESEP OBAT ---
            let resepIndex = 0;
            const obatList = @json($obat);

            function addResepRow() {
                const options = obatList.length > 0
                    ? obatList.map(o => `<option value="${o.id_obat}">${o.nama_obat} (stok: ${o.stok[0] ? o.stok[0].jumlah : 0})</option>`).join('')
                    : '<option value="">Tidak ada obat tersedia</option>';
                
                // [PERBAIKAN] Input 'aturan_pakai' dihapus
                const newResep = `
                    <div class="row g-2 mb-2 align-items-center resep-entry" id="resep-entry-${resepIndex}">
                        <div class="col-sm-8">
                            <select name="obat[${resepIndex}][id_obat]" class="form-select select-obat" data-placeholder="Pilih Obat..." required>
                                <option></option>
                                ${options}
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <input type="number" name="obat[${resepIndex}][jumlah]" class="form-control" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-sm-1 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-resep"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                `;
                $('#resep-obat-container').append(newResep);
                $(`#resep-entry-${resepIndex} .select-obat`).select2({ theme: 'bootstrap-5' });
                resepIndex++;
            }

            if (obatList.length === 0) {
                $('#resep-obat-container').html('<div class="alert alert-warning">Tidak ada obat yang tersedia di lokasi Anda.</div>');
                $('#add-resep').prop('disabled', true);
            } else {
                addResepRow();
                $('#add-resep').on('click', addResepRow);
            }
            
            $('#resep-obat-container').on('click', '.remove-resep', function() {
                $(this).closest('.resep-entry').remove();
            });
        });
    </script>
@endpush    