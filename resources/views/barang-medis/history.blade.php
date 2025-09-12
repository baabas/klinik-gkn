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
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td>{{ $history->created_at->format('d-m-Y H:i') }}</td>
                                <td>{{ $history->lokasi->nama_lokasi ?? '-' }}</td>
                                <td>{{ $history->perubahan }}</td>
                                <td>{{ $history->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada riwayat stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route('barang-medis.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
@endsection
