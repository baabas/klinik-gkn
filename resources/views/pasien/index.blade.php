@extends('layouts.sidebar-layout')

@section('content')
    <h1 class="h2 mb-4">Daftar Akun Pasien</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                
                <form action="{{ route('pasien.index') }}" method="GET" class="d-flex" style="width: 300px;">
                    <input type="search" class="form-control me-2" name="search" placeholder="Cari Nama atau NIP..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>No. Index</th>
                            <th>NIP</th>
                            <th>Nama Karyawan</th>
                            <th>Jabatan</th>
                            <th>Kantor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasien as $p)
                        <tr>
                            <td>{{ $loop->iteration + $pasien->firstItem() - 1 }}</td>
                            <td>{{ $p->id }}</td>
                            <td>{{ $p->nip }}</td>
                            <td>{{ $p->nama_karyawan }}</td>
                            <td>{{ $p->karyawan->jabatan ?? '-' }}</td>
                            <td>{{ $p->karyawan->kantor ?? '-' }}</td>
                            <td>
                                <a href="{{ route('pasien.show', $p->nip) }}" class="btn btn-info btn-sm">Lihat Kartu</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data pasien ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $pasien->links() }}
            </div>
        </div>
    </div>
@endsection
