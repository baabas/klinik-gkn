@extends('layouts.sidebar-layout')

@section('title', 'Master WhatsApp Validator')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="bi bi-whatsapp"></i> Master WhatsApp Validator
                </h4>
                <a href="{{ route('master-whatsapp-validator.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Tambah Validator
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
                                    <th width="25%">Nama Validator</th>
                                    <th width="20%">Nomor WhatsApp</th>
                                    <th width="30%">Keterangan</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($validators as $index => $validator)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $validator->nama_validator }}</td>
                                        <td>
                                            <i class="bi bi-whatsapp text-success"></i> {{ $validator->nomor_wa }}
                                        </td>
                                        <td>{{ $validator->keterangan ?? '-' }}</td>
                                        <td>
                                            @if($validator->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('master-whatsapp-validator.edit', $validator->id) }}" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('master-whatsapp-validator.destroy', $validator->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus validator ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Belum ada data validator
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
