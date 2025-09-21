@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h2 mb-0">Edit Draft Permintaan</h1>
            <p class="text-muted mb-0">Perbarui detail permintaan sebelum diajukan.</p>
        </div>
        <a href="{{ route('permintaan.show', $permintaan) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
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
        'action' => route('permintaan.update', $permintaan),
        'method' => 'PUT',
        'permintaan' => $permintaan,
    ])
@endsection
