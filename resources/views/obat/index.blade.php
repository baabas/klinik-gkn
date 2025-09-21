@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Stok Obat</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Obat</h5>
            <a href="{{ route('obat.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Tambah Obat Baru
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Obat</th>
                            <th>Stok</th>
                            <th>Satuan Dasar</th>
                            <th>Kemasan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($obat as $item)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration + $obat->firstItem() - 1 }}</td>
                            <td>{{ $item->nama_obat }}</td>
                            <td>{{ $item->stok_saat_ini }}</td>
                            <td>{{ $item->satuan_dasar }}</td>
                            <td>{{ $item->kemasan }}</td>
                            <td>
                                <a href="{{ route('obat.edit', $item->id_obat) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </a>
                                <form action="{{ route('obat.destroy', $item->id_obat) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus obat ini?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data obat.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $obat->links() }}
            </div>
        </div>
    </div>
@endsection
