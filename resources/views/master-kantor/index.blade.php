@extends('layouts.sidebar-layout')

@section('title', 'Master Data Kantor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="bi bi-building"></i> Master Data Kantor
                </h4>
                <a href="{{ route('master-kantor.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Kantor
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Nama Kantor</th>
                                    <th width="20%">Kode</th>
                                    <th width="15%">Status</th>
                                    <th width="25%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kantors as $index => $kantor)
                                    <tr>
                                        <td>{{ $kantors->firstItem() + $index }}</td>
                                        <td>{{ $kantor->nama_kantor }}</td>
                                        <td>{{ $kantor->kode_kantor ?? '-' }}</td>
                                        <td>
                                            @if($kantor->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('master-kantor.edit', $kantor->id_kantor) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('master-kantor.destroy', $kantor->id_kantor) }}" 
                                                  method="POST" 
                                                  class="d-inline" 
                                                  onsubmit="return confirm('Yakin ingin menghapus kantor ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 text-muted mb-3 d-block"></i>
                                            <p class="text-muted">Belum ada data kantor</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($kantors->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $kantors->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
