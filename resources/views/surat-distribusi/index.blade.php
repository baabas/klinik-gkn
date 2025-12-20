@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Daftar Surat Distribusi</h1>
        <a href="{{ route('barang-medis.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Obat
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No. Surat</th>
                            <th>Tanggal</th>
                            <th>Dari</th>
                            <th>Ke</th>
                            <th class="text-center">Jumlah Item</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suratDistribusi as $surat)
                            <tr>
                                <td>
                                    <strong>{{ $surat->nomor_surat }}</strong>
                                    <br><small class="text-muted">{{ $surat->kode_validasi }}</small>
                                </td>
                                <td>{{ $surat->tanggal_distribusi->format('d/m/Y') }}</td>
                                <td>{{ $surat->lokasiAsal->nama_lokasi ?? '-' }}</td>
                                <td>{{ $surat->lokasiTujuan->nama_lokasi ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">{{ $surat->details->count() }} item</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('surat-distribusi.show', $surat->id_surat) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('surat-distribusi.preview-pdf', $surat->id_surat) }}" class="btn btn-sm btn-success btn-print-pdf" title="Print PDF" target="_blank" data-url="{{ route('surat-distribusi.preview-pdf', $surat->id_surat) }}">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <a href="{{ route('surat-distribusi.print-pdf', $surat->id_surat) }}" class="btn btn-sm btn-primary" title="Download PDF">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <div>Belum ada surat distribusi.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $suratDistribusi->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-print-pdf').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var pdfUrl = this.getAttribute('data-url');
            var printWindow = window.open(pdfUrl, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        });
    });
</script>
@endpush
