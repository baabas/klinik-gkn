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

<form action="{{ route('rekam-medis.store', $user->nip) }}" method="POST" id="rekamMedisForm">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            {{-- Bagian Utama Form --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0 form-section-title">1. Anamnesa (Pemeriksaan Subjektif)</h5></div>
                <div class="card-body">
                    <div class="row"><div class="col-md-6 mb-3">
                            <label for="tanggal_kunjungan" class="form-label fw-bold">Tanggal Kunjungan <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control @error('tanggal_kunjungan') is-invalid @enderror" id="tanggal_kunjungan" name="tanggal_kunjungan" value="{{ old('tanggal_kunjungan', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('tanggal_kunjungan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div></div>
                    <div class="mb-3">
                        <label for="riwayat_sakit" class="form-label fw-bold">Keluhan & Riwayat Sakit</label>
                        <textarea class="form-control" id="riwayat_sakit" name="riwayat_sakit" rows="4" placeholder="Contoh: Pasien datang dengan keluhan demam selama 3 hari, batuk, dan sakit tenggorokan.">{{ old('riwayat_sakit') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0 form-section-title">2. Assessment (Diagnosa)</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="diagnosa" class="form-label fw-bold">Diagnosa Penyakit <span class="text-danger">*</span></label>
                        <select id="diagnosa" name="diagnosa[]" class="form-select @error('diagnosa') is-invalid @enderror" multiple="multiple" required data-placeholder="Cari dan pilih diagnosa...">
                            @foreach($penyakit as $p)
                                <option value="{{ $p->kode_penyakit }}" {{ (collect(old('diagnosa'))->contains($p->kode_penyakit)) ? 'selected' : '' }}>
                                    {{ $p->nama_penyakit }}
                                </option>
                            @endforeach
                        </select>
                        @error('diagnosa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header"><h5 class="mb-0 form-section-title">3. Plan (Terapi & Resep Obat)</h5></div>
                <div class="card-body">
                    <label class="form-label fw-bold">Resep Obat</label>
                    <div id="resep-obat-container"></div>
                    <button type="button" id="add-resep" class="btn btn-sm btn-outline-primary mt-2"><i class="bi bi-plus-circle"></i> Tambah Obat</button>
                    <hr class="my-3">
                    <div class="mb-3">
                        <label for="pengobatan" class="form-label fw-bold">Catatan Pengobatan / Lainnya</label>
                        <textarea class="form-control" id="pengobatan" name="pengobatan" rows="3" placeholder="Contoh: Istirahat yang cukup, perbanyak minum air putih.">{{ old('pengobatan') }}</textarea>
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
                    <p class="card-text text-muted mb-0">NIP: {{ $user->nip }}</p>
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
                <a href="{{ route('pasien.show', $user->nip) }}" class="btn btn-outline-secondary">Batal</a>
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
            $('#diagnosa').select2({ theme: 'bootstrap-5', closeOnSelect: false });

            let resepIndex = 0;
            const obatList = @json($obat);

            function addResepRow() {
                resepIndex++;
                // ================== PERBAIKAN DI SINI (menghapus input dosis) ==================
                const newResep = `
                    <div class="row g-2 mb-2 align-items-center resep-entry" id="resep-entry-${resepIndex}">
                        <div class="col-sm-8">
                            <select name="obat[]" class="form-select select-obat" data-placeholder="Pilih Obat..." required><option></option>
                                ${obatList.map(o => `<option value="${o.id_obat}">${o.nama_obat}</option>`).join('')}
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="number" name="kuantitas[]" class="form-control" placeholder="Qty" min="1" required>
                        </div>
                        <div class="col-sm-2 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-resep"><i class="bi bi-x-lg"></i></button>
                        </div>
                    </div>
                `;
                // ==============================================================================
                $('#resep-obat-container').append(newResep);
                $(`#resep-entry-${resepIndex} .select-obat`).select2({ theme: 'bootstrap-5' });
            }

            $('#add-resep').on('click', addResepRow);
            if ($('#resep-obat-container').children().length === 0) { addResepRow(); }
            $('#resep-obat-container').on('click', '.remove-resep', function() { $(this).closest('.resep-entry').remove(); });
        });
    </script>
@endpush