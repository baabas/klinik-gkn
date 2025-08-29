@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Cetak Laporan PDF</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Pilih Laporan Bulanan</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('laporan.obat') }}" method="GET" target="_blank" class="mb-4">
                        <div class="mb-3">
                            <label for="filter_bulan_obat" class="form-label fw-bold">1. Laporan Pemakaian Obat</label>
                            <div class="input-group">
                                <input type="month" id="filter_bulan_obat" name="filter_bulan" class="form-control"
                                       value="{{ date('Y-m') }}"
                                       max="{{ date('Y-m') }}">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-printer"></i> Cetak PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    <form action="{{ route('laporan.penyakit-kunjungan') }}" method="GET" target="_blank">
                        <div class="mb-3">
                            <label for="filter_bulan_penyakit" class="form-label fw-bold">2. Laporan Penyakit dan Kunjungan</label>
                            <div class="input-group">
                                <input type="month" id="filter_bulan_penyakit" name="filter_bulan" class="form-control"
                                       value="{{ date('Y-m') }}"
                                       max="{{ date('Y-m') }}">
                                <button class="btn btn-success" type="submit">
                                    <i class="bi bi-printer"></i> Cetak PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-muted text-center">
                    <small>Laporan akan terbuka di tab baru.</small>
                </div>
            </div>
        </div>
    </div>
@endsection
