@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h2 mb-0">Buat Permintaan Barang</h1>
            <p class="text-muted mb-0">Isi detail obat yang diperlukan kemudian simpan sebagai draft.</p>
        </div>
        <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <h6 class="alert-heading">Validasi gagal</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('permintaan.partials.form', [
        'action' => route('permintaan.store'),
        'method' => 'POST',
        'permintaan' => null,
    ])
@endsection
