@extends('layouts.sidebar-layout')

@push('styles')
<style>
    /* Custom table styling */
    .table-responsive {
        border-radius: 0.375rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        position: relative;
    }
    
    .table th {
        background-color: #f8f9fa !important;
        font-weight: 600;
        font-size: 0.875rem;
        white-space: nowrap;
        border-bottom: 2px solid #dee2e6;
        vertical-align: middle;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .table th.sticky-end {
        position: sticky;
        right: 0;
        z-index: 11;
        box-shadow: -2px 0 4px rgba(0,0,0,0.1);
    }
    
    .table td {
        vertical-align: middle;
        font-size: 0.875rem;
        padding: 0.75rem 0.5rem;
    }
    
    .table td.sticky-action-column,
    .table td:last-child {
        position: sticky;
        right: 0;
        background-color: white;
        box-shadow: -2px 0 4px rgba(0,0,0,0.1);
        z-index: 5;
    }
    
    .table tbody tr:hover td.sticky-action-column,
    .table tbody tr:hover td:last-child {
        background-color: #f8f9fa;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Badge improvements */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Button group styling */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    /* Compact action buttons */
    .d-flex.gap-1 > * {
        margin-bottom: 2px;
    }
    
    /* Responsive font sizes */
    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.75rem;
            padding: 0.5rem 0.25rem;
        }
        
        .btn-sm {
            padding: 0.2rem 0.4rem;
            font-size: 0.7rem;
        }
    }
    
    /* Improved code styling */
    code {
        background-color: #e9ecef;
        color: #495057;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.85em;
    }
    
    /* Status indicators */
    .text-danger { color: #dc3545 !important; }
    .text-warning { color: #fd7e14 !important; }
    .text-success { color: #198754 !important; }
    .text-primary { color: #0d6efd !important; }
</style>
@endpush

@section('content')
    <h1 class="h2 mb-4">Obat & Alat Medis</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div class="btn-group">
                    <a href="{{ route('permintaan.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text"></i> Daftar Permintaan
                    </a>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clipboard-data"></i> Riwayat Barang Masuk
                    </a>
                    @if(Auth::user()->hasRole('PENGADAAN'))
                    <a href="{{ route('barang-masuk.create') }}" class="btn btn-success">
                            <i class="bi bi-box-arrow-in-down"></i> Input Barang Masuk
                        </a>
                        <a href="{{ route('barang-medis.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Tambah Barang Baru
                        </a>
                    @endif
                </div>

                <form action="{{ route('barang-medis.index') }}" method="GET" class="d-flex" style="max-width: 320px;">
                    <input type="search" class="form-control me-2" name="search" placeholder="Cari Nama atau Kode..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive" style="max-height: 80vh; overflow-y: auto;">
                <table class="table table-bordered table-striped table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th style="width: 100px;" class="text-center">Kode</th>
                            <th style="min-width: 200px;">Nama Obat/Alat Medis</th>
                            <th style="width: 100px;" class="text-center">Kategori</th>
                            <th style="width: 80px;" class="text-center">Kemasan</th>
                            <th style="width: 120px;" class="text-center">Isi Kemasan</th>
                            <th style="width: 120px;" class="text-center">Isi per Satuan</th>
                            <th style="width: 100px;" class="text-center">Satuan Terkecil</th>
                            <th style="width: 120px;" class="text-center">Tanggal Masuk Terakhir</th>
                            <th style="width: 120px;" class="text-center">Kadaluarsa Terdekat</th>
                            <th style="width: 90px;" class="text-center">Stok GKN 1</th>
                            <th style="width: 90px;" class="text-center">Stok GKN 2</th>
                            <th style="width: 90px;" class="text-center">Total Stok</th>
                            <th style="width: 200px;" class="text-center sticky-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barang as $item)
                            @php
                                $stokGkn1 = (int) ($item->stok_gkn1 ?? 0);
                                $stokGkn2 = (int) ($item->stok_gkn2 ?? 0);
                                $totalStok = (int) ($item->stok_sum_jumlah ?? 0);
                            @endphp
                            <tr class="align-middle">
                                <td class="text-center">{{ $loop->iteration + $barang->firstItem() - 1 }}</td>
                                <td class="text-center"><code>{{ $item->kode_obat }}</code></td>
                                <td>
                                    <div class="fw-semibold">{{ $item->nama_obat }}</div>
                                    <div class="text-muted small">Total: {{ $totalStok ? number_format($totalStok) : 0 }} {{ strtolower($item->satuan_terkecil ?? '') }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item->kategori_barang == 'Obat' ? 'bg-primary' : 'bg-success' }}">{{ $item->kategori_barang }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $item->kemasan ?? 'Box' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($item->isi_kemasan_jumlah && $item->isi_kemasan_satuan)
                                        <div><strong>{{ $item->isi_kemasan_jumlah }}</strong></div>
                                        <small class="text-muted">{{ $item->isi_kemasan_satuan }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->isi_per_satuan)
                                        <div><strong>{{ $item->isi_per_satuan }}</strong></div>
                                        @if($item->satuan_terkecil && $item->isi_kemasan_satuan)
                                            <small class="text-muted">{{ $item->satuan_terkecil }}/{{ $item->isi_kemasan_satuan }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->satuan_terkecil)
                                        <span class="badge bg-info text-dark">{{ $item->satuan_terkecil }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->tanggal_masuk_terakhir)
                                        <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($item->tanggal_masuk_terakhir)->format('d/m/Y') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->expired_terdekat)
                                        @php
                                            $expiredDate = \Illuminate\Support\Carbon::parse($item->expired_terdekat);
                                            $isExpired = $expiredDate->isPast();
                                            $isNearExpiry = $expiredDate->diffInDays(now()) <= 30 && !$isExpired;
                                        @endphp
                                        <small class="
                                            @if($isExpired) text-danger fw-bold
                                            @elseif($isNearExpiry) text-dark fw-bold
                                            @else text-muted
                                            @endif
                                        ">
                                            {{ $expiredDate->format('d/m/Y') }}
                                            @if($isExpired) 
                                                <i class="bi bi-exclamation-triangle-fill" title="Sudah Kadaluarsa"></i>
                                            @elseif($isNearExpiry) 
                                                <i class="bi bi-exclamation-triangle" title="Akan Segera Kadaluarsa"></i>
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center"><strong class="text-primary">{{ number_format($stokGkn1) }}</strong></td>
                                <td class="text-center"><strong class="text-success">{{ number_format($stokGkn2) }}</strong></td>
                                <td class="text-center"><strong class="text-dark">{{ number_format($totalStok) }}</strong></td>
                                <td class="text-center sticky-action-column">
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        {{-- Tombol yang bisa diakses semua role terkait (Dokter & Pengadaan) --}}
                                        <a href="{{ route('barang-medis.show', $item->id_obat) }}" class="btn btn-info btn-sm" title="Lihat Detail Stok"><i class="bi bi-eye"></i></a>

                                        {{-- Tombol Distribusi sekarang bisa diakses oleh Dokter dan Pengadaan --}}
                                        @if(Auth::user()->hasRole('DOKTER') || Auth::user()->hasRole('PENGADAAN'))
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#distribusiModal-{{ $item->id_obat }}" title="Distribusi Stok">
                                                <i class="bi bi-truck"></i>
                                            </button>
                                        @endif

                                        {{-- Tombol yang HANYA bisa diakses oleh Pengadaan --}}
                                        @if(Auth::user()->hasRole('PENGADAAN'))
                                            <a href="{{ route('barang-medis.edit', $item->id_obat) }}" class="btn btn-warning btn-sm" title="Edit Barang"><i class="bi bi-pencil-square"></i></a>
                                            <form action="{{ route('barang-medis.destroy', $item->id_obat) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Barang"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="13" class="text-center">Tidak ada data barang medis ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $barang->links() }}
            </div>
        </div>
    </div>

    {{-- Modal untuk Distribusi Stok (Tidak ada perubahan di sini) --}}
    @foreach ($barang as $item)
    <div class="modal fade" id="distribusiModal-{{ $item->id_obat }}" tabindex="-1" aria-labelledby="distribusiModalLabel-{{ $item->id_obat }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="distribusiModalLabel-{{ $item->id_obat }}">Distribusi Stok: {{ $item->nama_obat }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('barang-medis.distribusi', $item->id_obat) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="lokasi_asal-{{ $item->id_obat }}" class="form-label">Dari Lokasi</label>
                            <select class="form-select" name="lokasi_asal" id="lokasi_asal-{{ $item->id_obat }}" required>
                                <option value="1" data-stok="{{ (int)($item->stok_gkn1 ?? 0) }}">GKN 1 (Stok: {{ (int)($item->stok_gkn1 ?? 0) }})</option>
                                <option value="2" data-stok="{{ (int)($item->stok_gkn2 ?? 0) }}">GKN 2 (Stok: {{ (int)($item->stok_gkn2 ?? 0) }})</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="lokasi_tujuan-{{ $item->id_obat }}" class="form-label">Ke Lokasi</label>
                            <select class="form-select" name="lokasi_tujuan" id="lokasi_tujuan-{{ $item->id_obat }}" required>
                                 <option value="2">GKN 2</option>
                                 <option value="1">GKN 1</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah-{{ $item->id_obat }}" class="form-label">Jumlah Distribusi</label>
                            <input type="number" name="jumlah" id="jumlah-{{ $item->id_obat }}" class="form-control" required min="1" max="{{ (int)($item->stok_gkn1 ?? 0) }}">
                            <div class="form-text">
                                Stok tersedia di lokasi asal: <span class="stok-tersedia fw-bold">{{ (int)($item->stok_gkn1 ?? 0) }}</span>
                            </div>
                        </div>
                         <div class="alert alert-danger d-none" role="alert" id="warning-{{ $item->id_obat }}">
                            Jumlah distribusi tidak boleh melebihi stok yang tersedia dan lokasi tujuan tidak boleh sama.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn-{{ $item->id_obat }}">Simpan Distribusi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.modal').forEach(modal => {
        const lokasiAsalSelect = modal.querySelector('select[name="lokasi_asal"]');
        const lokasiTujuanSelect = modal.querySelector('select[name="lokasi_tujuan"]');
        const jumlahInput = modal.querySelector('input[name="jumlah"]');
        const stokTersediaSpan = modal.querySelector('.stok-tersedia');
        const submitBtn = modal.querySelector('button[type="submit"]');
        const warningAlert = modal.querySelector('.alert');
        function validateForm() {
            const asal = lokasiAsalSelect.value;
            const tujuan = lokasiTujuanSelect.value;
            const jumlah = parseInt(jumlahInput.value, 10) || 0;
            const maxStok = parseInt(jumlahInput.max, 10) || 0;
            let isValid = true;
            if (jumlah > maxStok || jumlah <= 0) {
                isValid = false;
            }
            if (asal === tujuan) {
                isValid = false;
            }
            if (!isValid) {
                warningAlert.classList.remove('d-none');
            } else {
                warningAlert.classList.add('d-none');
            }
            submitBtn.disabled = !isValid;
        }
        function updateStokTersedia() {
            const selectedOption = lokasiAsalSelect.options[lokasiAsalSelect.selectedIndex];
            const stok = selectedOption ? selectedOption.getAttribute('data-stok') : 0;
            stokTersediaSpan.textContent = stok;
            jumlahInput.max = stok;
            validateForm();
        }
        lokasiAsalSelect.addEventListener('change', updateStokTersedia);
        lokasiTujuanSelect.addEventListener('change', validateForm);
        jumlahInput.addEventListener('input', validateForm);
        updateStokTersedia();
    });
});
</script>
@endpush
