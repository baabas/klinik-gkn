@extends('layouts.sidebar-layout')

@section('title', 'Detail Penyakit')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Detail Penyakit</h1>
                    <p class="mb-0 text-muted">Informasi lengkap penyakit: {{ $penyakit->ICD10 }}</p>
                </div>
                <div>
                    <a href="{{ route('daftar-penyakit.edit', $penyakit->ICD10) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </a>
                    <a href="{{ route('daftar-penyakit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informasi Penyakit -->
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informasi Penyakit
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Kode ICD10:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span class="badge bg-primary fs-6">{{ $penyakit->ICD10 }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Nama Penyakit:</strong>
                                </div>
                                <div class="col-md-9">
                                    <h5 class="text-dark mb-0">{{ $penyakit->nama_penyakit }}</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Dibuat:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span class="text-muted">
                                        {{ $penyakit->created_at ? $penyakit->created_at->format('d F Y, H:i:s') : 'Tidak tersedia' }}
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Terakhir Diperbarui:</strong>
                                </div>
                                <div class="col-md-9">
                                    <span class="text-muted">
                                        {{ $penyakit->updated_at ? $penyakit->updated_at->format('d F Y, H:i:s') : 'Tidak tersedia' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik dan Aksi -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Statistik Penggunaan
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            @php
                                $diagnosisCount = $penyakit->detailDiagnosa->count();
                            @endphp
                            <div class="display-4 text-success mb-2">{{ $diagnosisCount }}</div>
                            <p class="text-muted mb-3">Kali digunakan dalam diagnosa</p>
                            
                            @if($diagnosisCount > 0)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Penyakit ini sudah digunakan dalam {{ $diagnosisCount }} diagnosa
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    <i class="fas fa-clock me-2"></i>
                                    Penyakit ini belum pernah digunakan dalam diagnosa
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header bg-dark text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>Aksi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('daftar-penyakit.edit', $penyakit->ICD10) }}" 
                                   class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Edit Penyakit
                                </a>
                                
                                @if($diagnosisCount == 0)
                                    <button type="button" 
                                            class="btn btn-danger" 
                                            onclick="confirmDelete('{{ $penyakit->ICD10 }}', '{{ addslashes($penyakit->nama_penyakit) }}')">
                                        <i class="fas fa-trash me-2"></i>Hapus Penyakit
                                    </button>
                                @else
                                    <button type="button" 
                                            class="btn btn-outline-danger" 
                                            disabled
                                            title="Tidak dapat dihapus karena sudah digunakan dalam diagnosa">
                                        <i class="fas fa-lock me-2"></i>Tidak Dapat Dihapus
                                    </button>
                                @endif
                                
                                <a href="{{ route('daftar-penyakit.create') }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Tambah Penyakit Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Diagnosa -->
            @if($penyakit->detailDiagnosa->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history me-2"></i>Riwayat Diagnosa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No. Rekam Medis</th>
                                            <th>Pasien</th>
                                            <th>Tanggal Diagnosa</th>
                                            <th>Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($penyakit->detailDiagnosa->take(10) as $index => $detail)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $detail->rekamMedis->no_rekam_medis ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($detail->rekamMedis && $detail->rekamMedis->karyawan)
                                                        <strong>{{ $detail->rekamMedis->karyawan->nama }}</strong>
                                                        <small class="text-muted d-block">
                                                            NIP: {{ $detail->rekamMedis->karyawan->nip }}
                                                        </small>
                                                    @elseif($detail->rekamMedis && $detail->rekamMedis->nonKaryawan)
                                                        <strong>{{ $detail->rekamMedis->nonKaryawan->nama }}</strong>
                                                        <small class="text-muted d-block">
                                                            NIK: {{ $detail->rekamMedis->nonKaryawan->nik }}
                                                        </small>
                                                    @else
                                                        <span class="text-muted">Data pasien tidak tersedia</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        {{ $detail->rekamMedis ? $detail->rekamMedis->created_at->format('d/m/Y H:i') : '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $detail->deskripsi ?? 'Tidak ada deskripsi' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            @if($penyakit->detailDiagnosa->count() > 10)
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        Menampilkan 10 dari {{ $penyakit->detailDiagnosa->count() }} diagnosa
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus penyakit:</p>
                <div class="alert alert-warning">
                    <strong id="delete-code"></strong> - <span id="delete-name"></span>
                </div>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Data yang sudah dihapus tidak dapat dikembalikan!
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(icd10, namaPenyakit) {
    document.getElementById('delete-code').textContent = icd10;
    document.getElementById('delete-name').textContent = namaPenyakit;
    document.getElementById('delete-form').action = '{{ url("daftar-penyakit") }}/' + icd10;
    
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush