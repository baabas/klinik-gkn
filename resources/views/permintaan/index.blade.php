@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h2 mb-0">Daftar Permintaan Barang</h1>
            <p class="text-muted mb-0">Pantau seluruh permintaan obat dari klinik.</p>
        </div>
        @if(auth()->user()?->hasRole('DOKTER'))
            <a href="{{ route('permintaan.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Buat Permintaan
            </a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('permintaan.index') }}" method="get" class="row gy-2 gx-2 gx-lg-3 align-items-end mb-4">
                <div class="col-12 col-md-6 col-xl-4">
                    <label for="search" class="form-label">Pencarian</label>
                    <input type="search" class="form-control" name="search" id="search"
                           placeholder="Cari kode, peminta, atau lokasi" value="{{ $filters['search'] }}">
                </div>
                <div class="col-12 col-md-4 col-xl-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2 col-xl-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary flex-fill">
                        <i class="bi bi-search"></i>
                    </button>
                    @if($filters['search'] || $filters['status'])
                        <a href="{{ route('permintaan.index') }}" class="btn btn-light flex-fill" title="Reset filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:5%">#</th>
                            <th style="width:15%">Kode</th>
                            <th style="width:15%">Tanggal</th>
                            <th>Peminta</th>
                            <th>Lokasi</th>
                            <th style="width:12%">Status</th>
                            <th style="width:20%" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permintaan as $item)
                            <tr>
                                <td>{{ $loop->iteration + $permintaan->firstItem() - 1 }}</td>
                                <td class="fw-semibold">{{ $item->kode }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d F Y') }}</td>
                                <td>{{ $item->peminta?->display_name ?? '-' }}</td>
                                <td>{{ $item->lokasi?->nama_lokasi ?? '-' }}</td>
                                <td>
                                    <span class="{{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex justify-content-end gap-1">
                                        <a href="{{ route('permintaan.show', $item) }}" class="btn btn-sm btn-outline-secondary" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(auth()->user()?->hasRole('DOKTER') && $item->isDraft() && $item->peminta_id === auth()->id())
                                            <a href="{{ route('permintaan.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif

                                        @if(auth()->user()?->hasRole(['PENGADAAN', 'ADMIN']))
                                            @if($item->isDiajukan())
                                                <form action="{{ route('permintaan.approve', $item) }}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                                        <i class="bi bi-check-circle"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('permintaan.reject', $item) }}" method="post">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Tolak" onclick="return confirm('Tolak permintaan ini?')">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                </form>
                                            @elseif($item->isDisetujui())
                                                <a href="{{ route('permintaan.show', $item) }}" class="btn btn-sm btn-outline-success" title="Penuhi permintaan">
                                                    <i class="bi bi-box-arrow-down"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada permintaan barang.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $permintaan->links() }}
            </div>
        </div>
    </div>
@endsection
