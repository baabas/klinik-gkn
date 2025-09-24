<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-t">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
          <div class="container-fluid">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <img src="{{ asset('images/logo-klinik.png') }}" alt="Logo Klinik" style="height: 30px;" class="me-2">
                Klinik GKN
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main-nav">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="main-nav">
                @if(Auth::user()->roles()->where('name', 'DOKTER')->exists())
                    {{-- MENU UNTUK DOKTER --}}
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ (request()->routeIs('pasien.index') || request()->routeIs('pasien.show')) ? 'active' : '' }}" href="{{ route('pasien.index') }}">Daftar Pasien</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('barang-medis.*') ? 'active' : '' }}" href="{{ route('barang-medis.index') }}">Obat & Alat Medis</a>
                        </li>
                         <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Laporan
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('laporan.harian') }}">Laporan Harian</a></li>
                                <li><a class="dropdown-item" href="{{ route('laporan.pemakaian_obat') }}">Pemakaian Obat</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('laporan.index') }}">Cetak Laporan PDF</a></li>
                            </ul>
                        </li>
                    </ul>
                @else
                    {{-- MENU UNTUK PASIEN --}}
                     <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pasien.my_card') ? 'active' : '' }}" href="{{ route('pasien.my_card') }}">Kartu Pasien Saya</a>
                        </li>
                    </ul>
                @endif

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        Hallo, {{ Auth::user()->nama_karyawan }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                                    Logout
                                </a>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
          </div>
        </nav>

        <main class="container-fluid py-4 px-4">
            @yield('content')
        </main>
    </body>
</html>
