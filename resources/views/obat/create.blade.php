@extends('layouts.sidebar-layout')

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Tambah Obat Baru</h1>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('obat.store') }}" method="POST">
                @csrf
                @include('obat.partials.form')
            </form>
        </div>
    </div>
@endsection
