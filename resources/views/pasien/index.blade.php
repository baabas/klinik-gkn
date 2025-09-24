@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h2">Daftar Pasien</h1>
        <a href="{{ route('non_karyawan.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill"></i> Daftar Pasien Baru (Umum)
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('pasien.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="search" class="form-control" name="search" placeholder="Cari berdasarkan Nama, NIP, atau NIK..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID Pasien (NIP/NIK)</th>
                            <th>Nama Pasien</th>
                            <th>Tipe</th>
                            <th>Tanggal Lahir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasien as $p)
                        <tr>
                            <td>{{ $loop->iteration + $pasien->firstItem() - 1 }}</td>
                            {{-- Tampilkan NIP jika ada, jika tidak, tampilkan NIK --}}
                            <td>{{ $p->nip ?? $p->nik }}</td>
                            <td>{{ $p->nama_karyawan }}</td>
                            <td>
                                {{-- Tampilkan badge berdasarkan apakah relasi 'karyawan' ada atau tidak --}}
                                @if($p->karyawan)
                                    <span class="badge bg-primary">Karyawan</span>
                                @else
                                    <span class="badge bg-success">Non-Karyawan</span>
                                @endif
                            </td>
                            <td>
                                {{-- Ambil tanggal lahir dari profil yang sesuai --}}
                                @php
                                    $tanggal_lahir = $p->karyawan?->tanggal_lahir ?? $p->nonKaryawan?->tanggal_lahir;
                                @endphp
                                {{ $tanggal_lahir ? \Carbon\Carbon::parse($tanggal_lahir)->isoFormat('D MMMM YYYY') : '-' }}
                            </td>
                            <td>
                                {{-- Arahkan ke rute detail yang sesuai --}}
                                @if($p->karyawan)
                                     <a href="{{ route('pasien.show', $p->nip) }}" class="btn btn-info btn-sm">Lihat Kartu</a>
                                @else
                                     <a href="{{ route('pasien.show_non_karyawan', $p->nik) }}" class="btn btn-info btn-sm">Lihat Kartu</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data pasien ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $pasien->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection