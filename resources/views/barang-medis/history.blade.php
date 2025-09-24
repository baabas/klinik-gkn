@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Riwayat Stok: {{ $barangMedi->nama_obat }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Perubahan Jumlah</th>
                            <th>Stok Sebelum & Sesudah</th>
                            <th>Detail Kemasan</th>
                            <th>Kedaluwarsa</th>
                            <th>Petugas</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td>{{ optional($history->tanggal_transaksi)->format('d-m-Y') ?? $history->created_at->format('d-m-Y H:i') }}</td>
                                <td>{{ $history->lokasi->nama_lokasi ?? '-' }}</td>
                                <td>{{ $history->perubahan }}</td>
                                <td>
                                    <div>Sebelum: {{ $history->stok_sebelum ?? '-' }}</div>
                                    <div>Sesudah: {{ $history->stok_sesudah ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($history->jumlah_kemasan)
                                        {{ number_format($history->jumlah_kemasan) }} {{ $history->satuan_kemasan ?? 'kemasan' }}
                                        <div class="text-muted small">Isi {{ number_format($history->isi_per_kemasan) }} {{ strtolower($history->barang->satuan ?? '') }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ optional($history->expired_at)->format('d-m-Y') ?? '-' }}</td>
                                <td>{{ $history->user->name ?? '-' }}</td>
                                <td>{{ $history->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada riwayat stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route('barang-medis.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
