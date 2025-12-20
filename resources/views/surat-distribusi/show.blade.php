@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Detail Surat Distribusi</h1>
        <div>
            <a href="{{ route('surat-distribusi.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('surat-distribusi.preview-pdf', $surat->id_surat) }}" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-pdf"></i> Preview PDF
            </a>
            <button type="button" class="btn btn-success" id="printPdfBtn">
                <i class="bi bi-printer"></i> Print PDF
            </button>
            <a href="{{ route('surat-distribusi.print-pdf', $surat->id_surat) }}" class="btn btn-primary">
                <i class="bi bi-download"></i> Download PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Info Surat --}}
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>Informasi Surat Distribusi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>No. Surat</strong></td>
                                    <td>: {{ $surat->nomor_surat }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kode Validasi</strong></td>
                                    <td>: <code class="fs-5">{{ $surat->kode_validasi }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal</strong></td>
                                    <td>: {{ $surat->tanggal_distribusi->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $surat->user->nama_karyawan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Dari</strong></td>
                                    <td>: {{ $surat->lokasiAsal->nama_lokasi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tujuan</strong></td>
                                    <td>: {{ $surat->lokasiTujuan->nama_lokasi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>No. WA Validator</strong></td>
                                    <td>
                                        : {{ $surat->nomor_wa_validator }}
                                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" data-bs-toggle="modal" data-bs-target="#editWaModal" title="Edit Nomor WA">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($surat->catatan)
                        <div class="alert alert-light mt-3">
                            <strong>Catatan:</strong> {{ $surat->catatan }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Daftar Barang --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Daftar Barang</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" width="50">No</th>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($surat->details as $index => $detail)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $detail->barang->nama_obat ?? '-' }}</strong>
                                            <br><small class="text-muted">{{ $detail->barang->kode_obat ?? '' }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $detail->barang->kategori_barang == 'Obat' ? 'bg-primary' : 'bg-success' }}">
                                                {{ $detail->barang->kategori_barang ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center"><strong>{{ number_format($detail->jumlah) }}</strong></td>
                                        <td class="text-center">{{ $detail->barang->satuan_terkecil ?? 'Pcs' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total Item:</strong></td>
                                    <td class="text-center"><strong>{{ $surat->details->count() }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Info Distribusi --}}
            <div class="card shadow-sm border-success mb-4">
                <div class="card-body text-center py-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                    <h5 class="mt-2 text-success">Distribusi Berhasil</h5>
                    <p class="text-muted small mb-0">
                        Stok sudah otomatis dipindahkan ke lokasi tujuan.<br>
                        Surat ini sebagai bukti pemberitahuan distribusi.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Nomor WA --}}
    <div class="modal fade" id="editWaModal" tabindex="-1" aria-labelledby="editWaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editWaModalLabel">
                        <i class="bi bi-whatsapp me-2"></i>Edit Nomor WA Validator
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('surat-distribusi.update-wa', $surat->id_surat) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomor_wa_validator" class="form-label">Nomor WhatsApp Validator</label>
                            <select class="form-select" 
                                    id="nomor_wa_validator" 
                                    name="nomor_wa_validator" 
                                    required>
                                <option value="">-- Pilih Validator --</option>
                                @foreach($validators as $validator)
                                    <option value="{{ $validator->nomor_wa }}" 
                                            {{ $surat->nomor_wa_validator == $validator->nomor_wa ? 'selected' : '' }}>
                                        {{ $validator->nama_validator }} ({{ $validator->nomor_wa }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                Pilih validator yang akan menerima konfirmasi via WhatsApp
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.getElementById('printPdfBtn').addEventListener('click', function() {
        var pdfUrl = "{{ route('surat-distribusi.preview-pdf', $surat->id_surat) }}";
        var printWindow = window.open(pdfUrl, '_blank');
        printWindow.onload = function() {
            printWindow.print();
        };
    });
</script>
@endpush
