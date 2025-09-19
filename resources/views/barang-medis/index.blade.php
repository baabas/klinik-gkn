@extends('layouts.sidebar-layout')

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

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Obat/Alat Medis</th>
                            <th>Tipe</th>
                            <th>Tgl Masuk Terakhir</th>
                            <th>Kedaluwarsa Terdekat</th>
                            <th>Total Kemasan Masuk</th>
                            <th>Detail Kemasan Terakhir</th>
                            <th>Stok GKN 1</th>
                            <th>Stok GKN 2</th>
                            <th>Total Stok</th>
                            <th>Satuan</th>
                            <th style="width: 240px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barang as $item)
                        @php
                                $lastEntry = $item->stokMasukTerakhir;
                                $lastDate = $item->tanggal_masuk_terakhir ?? optional($lastEntry?->tanggal_transaksi)->toDateString();
                                $nearestExpired = $item->expired_terdekat ?? optional($lastEntry?->expired_at)->toDateString();
                                $totalKemasan = $item->total_kemasan_masuk ?? 0;
                                $satuanKemasan = $lastEntry?->satuan_kemasan ?: ($item->kemasan ?: 'Kemasan');
                                $stokGkn1 = (int) ($item->stok_gkn1 ?? 0);
                                $stokGkn2 = (int) ($item->stok_gkn2 ?? 0);
                                $totalStok = (int) ($item->stok_sum_jumlah ?? 0);
                            @endphp
                            <tr class="align-middle">
                                <td>{{ $loop->iteration + $barang->firstItem() - 1 }}</td>
                                <td>{{ $item->kode_obat }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $item->nama_obat }}</div>
                                    <div class="text-muted small">{{ $totalStok ? number_format($totalStok) : 0 }} {{ strtolower($item->satuan ?? '') }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $item->tipe == 'OBAT' ? 'bg-primary' : 'bg-success' }}">{{ $item->tipe }}</span>
                                </td>
                                <td>{{ $lastDate ? \Illuminate\Support\Carbon::parse($lastDate)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $nearestExpired ? \Illuminate\Support\Carbon::parse($nearestExpired)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    {{ $totalKemasan ? number_format($totalKemasan) : '0' }}
                                    <span class="text-muted small d-block">{{ $satuanKemasan }}</span>
                                </td>
                                <td>
                                    @if($lastEntry)
                                        <div>{{ number_format($lastEntry->jumlah_kemasan) }} {{ $lastEntry->satuan_kemasan ?? 'kemasan' }}</div>
                                        <div class="text-muted small">Isi {{ number_format($lastEntry->isi_per_kemasan) }} {{ strtolower($item->satuan ?? '') }}</div>
                                        @if($lastEntry->expired_at)
                                            <div class="text-muted small">Exp: {{ $lastEntry->expired_at->format('d/m/Y') }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">Belum ada data masuk</span>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($stokGkn1) }}</strong></td>
                                <td><strong>{{ number_format($stokGkn2) }}</strong></td>
                                <td><strong>{{ number_format($totalStok) }}</strong></td>
                                <td>{{ $item->satuan }}</td>
                                <td>
                                    {{-- Tombol yang bisa diakses semua role terkait (Dokter & Pengadaan) --}}
                                    <a href="{{ route('barang-medis.show', $item->id_obat) }}" class="btn btn-info btn-sm" title="Lihat Detail Stok"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('barang-medis.history', $item) }}" class="btn btn-secondary btn-sm" title="Riwayat Stok"><i class="bi bi-clock-history"></i></a>

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
