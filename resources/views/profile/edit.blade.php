@php
    $userRole = Auth::user()->akses ?? 'PASIEN';
    $layout = ($userRole === 'PASIEN') ? 'layouts.pasien-layout' : 'layouts.app';
@endphp

@extends($layout)

@section('title', 'Pengaturan Profile')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-gear-fill text-primary me-2"></i>
            Pengaturan Profile
        </h1>
        <div>
            @if($userRole === 'PASIEN')
                <a href="{{ route('pasien.my_card') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Kartu Pasien
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- Update Profile Information --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        {{-- Update Password --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>

        {{-- Delete Account --}}
        <div class="col-12">
            <div class="card shadow-sm border-danger">
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
