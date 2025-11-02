@extends('layouts.app')

@section('title', 'Laporan Feedback Pasien')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-chat-left-heart text-primary"></i> Laporan Feedback Pasien
        </h1>
    </div>

    {{-- Statistics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Total Feedback</div>
                    <h3 class="mb-0 fw-bold text-primary">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Rata-rata</div>
                    <h3 class="mb-0 fw-bold text-warning">
                        {{ $stats['rata_rata'] }}
                        <i class="bi bi-star-fill" style="font-size: 0.7em;"></i>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <div class="h1 mb-0">😍</div>
                    <div class="small text-muted">Sangat Puas</div>
                    <div class="fw-bold">{{ $stats['sangat_puas'] }} ({{ $stats['persentase']['sangat_puas'] }}%)</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <div class="h1 mb-0">😊</div>
                    <div class="small text-muted">Puas</div>
                    <div class="fw-bold">{{ $stats['puas'] }} ({{ $stats['persentase']['puas'] }}%)</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <div class="h1 mb-0">😐</div>
                    <div class="small text-muted">Cukup</div>
                    <div class="fw-bold">{{ $stats['cukup'] }} ({{ $stats['persentase']['cukup'] }}%)</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm h-100 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <div class="h1 mb-0">😞</div>
                    <div class="small text-muted">Tidak Puas</div>
                    <div class="fw-bold">{{ $stats['tidak_puas'] + $stats['sangat_tidak_puas'] }} ({{ $stats['persentase']['tidak_puas'] + $stats['persentase']['sangat_tidak_puas'] }}%)</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('feedback.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label small">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label small">Tanggal Akhir</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label for="rating" class="form-label small">Rating</label>
                    <select class="form-select" id="rating" name="rating">
                        <option value="">Semua Rating</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ Sangat Puas</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ Puas</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ Cukup</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ Tidak Puas</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ Sangat Tidak Puas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    @if(request()->hasAny(['start_date', 'end_date', 'rating']))
                        <a href="{{ route('feedback.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Feedback List --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Daftar Feedback</h5>
        </div>
        <div class="card-body p-0">
            @if($feedbacks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th style="width: 15%;">Tanggal</th>
                                <th style="width: 20%;">Pasien</th>
                                <th style="width: 15%;">ID</th>
                                <th style="width: 10%;">Rating</th>
                                <th style="width: 35%;">Komentar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedbacks as $index => $feedback)
                                <tr>
                                    <td>{{ $feedbacks->firstItem() + $index }}</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $feedback->waktu_feedback->format('d/m/Y') }}<br>
                                            <span class="text-muted">{{ $feedback->waktu_feedback->format('H:i') }}</span>
                                        </small>
                                    </td>
                                    <td>
                                        @if($feedback->rekamMedis->pasien && $feedback->rekamMedis->pasien->karyawan)
                                            <strong>{{ $feedback->rekamMedis->pasien->karyawan->nama }}</strong>
                                        @elseif($feedback->rekamMedis->pasienNonKaryawan)
                                            <strong>{{ $feedback->rekamMedis->pasienNonKaryawan->nama }}</strong>
                                        @else
                                            <em class="text-muted">Tidak diketahui</em>
                                        @endif
                                    </td>
                                    <td>
                                        @if($feedback->nip_pasien)
                                            <span class="badge bg-primary">NIP: {{ $feedback->nip_pasien }}</span>
                                        @elseif($feedback->nik_pasien)
                                            <span class="badge bg-secondary">NIK: {{ $feedback->nik_pasien }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span style="font-size: 1.5em;">{{ $feedback->rating_emoji }}</span>
                                            <div class="ms-2">
                                                <div class="fw-bold">{{ $feedback->rating }}/5</div>
                                                <small class="text-muted">{{ $feedback->rating_label }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($feedback->komentar)
                                            <small>{{ Str::limit($feedback->komentar, 100) }}</small>
                                        @else
                                            <em class="text-muted small">Tidak ada komentar</em>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Menampilkan {{ $feedbacks->firstItem() }} - {{ $feedbacks->lastItem() }} dari {{ $feedbacks->total() }} feedback
                        </div>
                        <div>
                            {{ $feedbacks->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">Belum ada feedback</p>
                    @if(request()->hasAny(['start_date', 'end_date', 'rating']))
                        <a href="{{ route('feedback.index') }}" class="btn btn-outline-primary btn-sm">
                            Reset Filter
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .table tbody tr {
        transition: background-color 0.2s;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
</style>
@endpush
@endsection
