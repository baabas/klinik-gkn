<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-t">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Klinik GKN') - {{ config('app.name', 'Klinik GKN') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
        
        @stack('styles')
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
                            <a class="nav-link {{ request()->routeIs('daftar-penyakit.*') ? 'active' : '' }}" href="{{ route('daftar-penyakit.index') }}">Daftar Penyakit</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('barang-medis.*') ? 'active' : '' }}" href="{{ route('barang-medis.index') }}">Obat & Alat Medis</a>
                        </li>
                        <li class="nav-item dropdown" id="laporan-nav-item">
                            <div class="d-flex align-items-center" id="laporan-nav">
                                <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.harian') }}">
                                    Laporan
                                </a>
                                <button class="btn btn-sm text-white p-1 ms-1" type="button" id="laporan-dropdown-toggle" aria-expanded="false" aria-haspopup="true" style="background: none; border: none;">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            <ul class="dropdown-menu" id="laporan-dropdown-menu" style="display: none;">
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

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1"></i> Hallo, {{ Auth::user()->nama_karyawan }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person-gear me-2"></i>Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </a>
                            </form>
                        </li>
                    </ul>
                </li>
            </div>
          </div>
        </nav>

        <main class="container-fluid py-4 px-4">
            @yield('content')
        </main>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Fix Dropdown Issues -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize all dropdown toggles
                var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
                var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                    return new bootstrap.Dropdown(dropdownToggleEl);
                });
                
                // Debug dropdown functionality
                const profileDropdownToggle = document.getElementById('navbarDropdown');
                const profileDropdownMenu = profileDropdownToggle ? profileDropdownToggle.nextElementSibling : null;
                const laporanToggle = document.getElementById('laporan-dropdown-toggle');
                const laporanMenu = document.getElementById('laporan-dropdown-menu');

                const closeProfileDropdown = () => {
                    if (profileDropdownMenu) {
                        profileDropdownMenu.classList.remove('show');
                    }
                    if (profileDropdownToggle) {
                        profileDropdownToggle.setAttribute('aria-expanded', 'false');
                    }
                };

                const closeLaporanMenu = () => {
                    if (laporanMenu) {
                        laporanMenu.style.display = 'none';
                        laporanMenu.classList.remove('show');
                    }
                    if (laporanToggle) {
                        laporanToggle.setAttribute('aria-expanded', 'false');
                    }
                };

                if (profileDropdownToggle && profileDropdownMenu) {
                    profileDropdownToggle.addEventListener('click', function(e) {
                        e.preventDefault();

                        const isOpen = profileDropdownMenu.classList.contains('show');
                        closeLaporanMenu();

                        if (isOpen) {
                            closeProfileDropdown();
                        } else {
                            profileDropdownMenu.style.position = 'absolute';
                            profileDropdownMenu.style.top = '100%';
                            profileDropdownMenu.style.right = '0';
                            profileDropdownMenu.style.left = 'auto';
                            profileDropdownMenu.style.zIndex = '10000';
                            profileDropdownMenu.classList.add('show');
                            profileDropdownToggle.setAttribute('aria-expanded', 'true');
                        }
                    });

                    document.addEventListener('click', function(e) {
                        if (!profileDropdownToggle.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
                            closeProfileDropdown();
                        }
                    });

                    profileDropdownMenu.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }

                if (laporanToggle && laporanMenu) {
                    laporanToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const isOpen = laporanMenu.style.display === 'block';
                        closeProfileDropdown();

                        if (isOpen) {
                            closeLaporanMenu();
                        } else {
                            laporanMenu.style.display = 'block';
                            laporanMenu.style.position = 'absolute';
                            laporanMenu.style.top = '100%';
                            laporanMenu.style.left = '0';
                            laporanMenu.style.zIndex = '9999';
                            laporanMenu.classList.add('show');
                            laporanToggle.setAttribute('aria-expanded', 'true');
                        }
                    });

                    document.addEventListener('click', function(e) {
                        if (!laporanToggle.contains(e.target) && !laporanMenu.contains(e.target)) {
                            closeLaporanMenu();
                        }
                    });

                    laporanMenu.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            });
        </script>
        
        @stack('scripts')
    </body>
</html>
