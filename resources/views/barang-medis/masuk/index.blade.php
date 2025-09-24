@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Riwayat Transaksi Obat & Alat Medis</h1>
        <div class="btn-group">
            <a href="{{ route('barang-medis.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Barang
            </a>
            @if(Auth::user()->hasRole('PENGADAAN'))
                <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Input Barang Masuk
                </a>
            @endif
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('barang-masuk.index') }}" method="GET" class="row g-3 align-items-end mb-3">
                <div class="col-md-4">
                    <label for="q" class="form-label">Cari Nama/Kode</label>
                    <input type="search" name="q" id="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Contoh: Paracetamol">
                </div>
                <div class="col-md-3">
                    <label for="barang" class="form-label">Filter Barang</label>
                    <select name="barang" id="barang" class="form-select">
                        <option value="">Semua Barang</option>
                        @foreach($barang as $item)
                            <option value="{{ $item->id_obat }}" {{ request('barang') == $item->id_obat ? 'selected' : '' }}>
                                {{ $item->nama_obat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tanggal" class="form-label">Tanggal Masuk</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}" class="form-control">
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Masuk</th>
                            <th>Nama Barang</th>
                            <th>Lokasi</th>
                            <th>Jumlah Kemasan</th>
                            <th>Isi per Kemasan</th>
                            <th>Total (Satuan)</th>
                            <th>Kedaluwarsa</th>
                            <th>Petugas</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            <tr class="align-middle">
                                <td>{{ $loop->iteration + $entries->firstItem() - 1 }}</td>
                                <td>
                                    {{ optional($entry->tanggal_transaksi)->format('d/m/Y') ?? $entry->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    <strong>{{ $entry->barang->nama_obat ?? '-' }}</strong>
                                    <div class="text-muted small">Kode: {{ $entry->barang->kode_obat ?? '-' }}</div>
                                </td>
                                <td>{{ $entry->lokasi->nama_lokasi ?? '-' }}</td>
                                <td>
                                    @if($entry->jumlah_kemasan)
                                        @if($entry->perubahan < 0)
                                            <span class="text-danger">-{{ number_format($entry->jumlah_kemasan) }}</span>
                                        @else
                                            <span class="text-success">{{ number_format($entry->jumlah_kemasan) }}</span>
                                        @endif
                                        @if($entry->satuan_kemasan)
                                            <span class="text-muted small d-block">{{ $entry->satuan_kemasan }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($entry->isi_per_kemasan)
                                        {{ number_format($entry->isi_per_kemasan) }} {{ strtolower($entry->barang->satuan ?? '') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($entry->perubahan > 0)
                                        <span class="text-success">+{{ number_format($entry->perubahan) }}</span>
                                    @elseif($entry->perubahan < 0)
                                        <span class="text-danger">{{ number_format($entry->perubahan) }}</span>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                    {{ strtolower($entry->barang->satuan ?? '') }}
                                </td>
                                <td>
                                    {{ optional($entry->expired_at)->format('d/m/Y') ?? '-' }}
                                </td>
                                <td>{{ $entry->user->nama_karyawan ?? '-' }}</td>
                                <td>{{ $entry->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Belum ada data barang masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $entries->links() }}
            </div>
        </div>
    </div>
@endsection
