@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Obat & Alat Medis</h1>

    <div class="card shadow-sm">
        <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="btn-group">
        <a href="{{ route('permintaan.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-file-earmark-text"></i> Daftar Permintaan
        </a>
        @if(Auth::user()->hasRole('PENGADAAN'))
            {{-- Tombol ini sekarang akan mengarah ke form create yang benar --}}
            <a href="{{ route('barang-medis.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Barang Baru
            </a>
        @endif
    </div>

                {{-- Form Pencarian di Kanan --}}
                <form action="{{ route('barang-medis.index') }}" method="GET" class="d-flex" style="width: 300px;">
                    <input type="search" class="form-control me-2" name="search" placeholder="Cari Nama atau Kode..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>


            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Tipe</th>
                            <th>Stok GKN 1</th>
                            <th>Stok GKN 2</th>
                            <th>Total Stok</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barang as $item)
                            <tr class="align-middle">
                                <td>{{ $loop->iteration + $barang->firstItem() - 1 }}</td>
                                <td>{{ $item->kode_obat }}</td>
                                <td>{{ $item->nama_obat }}</td>
                                <td>
                                    <span class="badge {{ $item->tipe == 'OBAT' ? 'bg-primary' : 'bg-success' }}">
                                        {{ $item->tipe }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ (int) ($item->stok_gkn1 ?? 0) }}</strong>
                                </td>
                                <td>
                                    <strong>{{ (int) ($item->stok_gkn2 ?? 0) }}</strong>
                                </td>
                                <td>
                                    <strong>{{ (int) $item->stok_sum_jumlah }}</strong>
                                </td>
                                <td>{{ $item->satuan }}</td>
                                <td>
                                    <a href="{{ route('barang-medis.show', $item->id_obat) }}" class="btn btn-info btn-sm" title="Lihat Detail Stok"><i class="bi bi-eye"></i></a>

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
                                <td colspan="9" class="text-center">Tidak ada data barang medis ditemukan.</td>
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
@endsection
