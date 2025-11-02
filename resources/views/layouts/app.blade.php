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
        @php
            $activeRole = session('active_role');

            if (!isset($activeRole) && Auth::check()) {
                $activeRole = Auth::user()->roles()->pluck('name')->first();
            }
        @endphp

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
                    @if($activeRole === 'DOKTER' && Auth::user()->roles()->where('name', 'DOKTER')->exists())
                        {{-- MENU UNTUK DOKTER --}}
                        <ul class="navbar-nav flex-row flex-wrap align-items-center gap-1 me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link text-white px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-primary shadow-sm' : '' }}"
                                   href="{{ route('dashboard') }}"
                                   style="transition: all 0.3s ease; font-weight: 500; text-decoration: none; {{ request()->routeIs('dashboard') ? '' : 'opacity: 0.85;' }}">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard Dokter
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white px-3 py-2 rounded {{ (request()->routeIs('pasien.index') || request()->routeIs('pasien.show')) ? 'bg-primary shadow-sm' : '' }}"
                                   href="{{ route('pasien.index') }}"
                                   style="transition: all 0.3s ease; font-weight: 500; text-decoration: none; {{ (request()->routeIs('pasien.index') || request()->routeIs('pasien.show')) ? '' : 'opacity: 0.85;' }}">
                                    <i class="bi bi-people-fill me-2"></i>Daftar Pasien
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white px-3 py-2 rounded {{ request()->routeIs('daftar-penyakit.*') ? 'bg-primary shadow-sm' : '' }}"
                                   href="{{ route('daftar-penyakit.index') }}"
                                   style="transition: all 0.3s ease; font-weight: 500; text-decoration: none; {{ request()->routeIs('daftar-penyakit.*') ? '' : 'opacity: 0.85;' }}">
                                    <i class="bi bi-clipboard-data-fill me-2"></i>Daftar Penyakit
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white px-3 py-2 rounded {{ request()->routeIs('barang-medis.*') ? 'bg-primary shadow-sm' : '' }}"
                                   href="{{ route('barang-medis.index') }}"
                                   style="transition: all 0.3s ease; font-weight: 500; text-decoration: none; {{ request()->routeIs('barang-medis.*') ? '' : 'opacity: 0.85;' }}">
                                    <i class="bi bi-archive-fill me-2"></i>Obat &amp; Alat Medis
                                </a>
                            </li>
                            <li class="nav-item dropdown" id="laporan-nav-item">
                                <div class="d-flex align-items-center" id="laporan-nav">
                                    <a class="nav-link text-white px-3 py-2 rounded {{ request()->routeIs('laporan.*') ? 'bg-primary shadow-sm' : '' }}"
                                       href="{{ route('laporan.harian') }}"
                                       style="transition: all 0.3s ease; font-weight: 500; text-decoration: none; {{ request()->routeIs('laporan.*') ? '' : 'opacity: 0.85;' }}">
                                        <i class="bi bi-printer-fill me-2"></i>Laporan
                                    </a>
                                    <button class="btn btn-sm text-white p-1 ms-1" type="button" id="laporan-dropdown-toggle" aria-expanded="false" aria-haspopup="true" style="background: none; border: none; font-size: 0.8rem;">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>
                                <ul class="dropdown-menu shadow-lg border-0" id="laporan-dropdown-menu" style="min-width: 220px; display: none;">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('laporan.harian') }}">
                                            <i class="bi bi-calendar-check me-2 text-primary"></i>Laporan Harian
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('laporan.pemakaian_obat') }}">
                                            <i class="bi bi-capsule-pill me-2 text-success"></i>Pemakaian Obat
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('laporan.index') }}">
                                            <i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i>Cetak Laporan PDF
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    @elseif($activeRole === 'PASIEN' && Auth::user()->roles()->where('name', 'PASIEN')->exists())
                        {{-- MENU UNTUK PASIEN --}}
                        <ul class="navbar-nav flex-row align-items-center gap-1 me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link text-white px-3 py-2 rounded {{ request()->routeIs('pasien.my_card') ? 'bg-primary shadow-sm' : '' }}"
                                   href="{{ route('pasien.my_card') }}"
                                   style="transition: all 0.3s ease; font-weight: 500; text-decoration: none; {{ request()->routeIs('pasien.my_card') ? '' : 'opacity: 0.85;' }}">
                                    <i class="bi bi-credit-card-2-front-fill me-2"></i>Kartu Pasien Saya
                                </a>
                            </li>
                        </ul>
                    @else
                        <div class="me-auto"></div>
                    @endif

                    @if(Auth::check())
                        <ul class="navbar-nav ms-auto align-items-center gap-3">
                            <li class="nav-item d-none d-md-flex flex-column align-items-end text-white-50 me-2">
                                <small class="text-light opacity-75" id="current-datetime" style="font-family: 'Courier New', monospace;">
                                    {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}
                                </small>
                            </li>
                            <li class="nav-item d-none d-sm-block text-white-50">
                                <span>Hallo, {{ Auth::user()->nama_karyawan }}</span>
                            </li>
                            <li class="nav-item dropdown" style="position: relative;">
                                <a class="nav-link dropdown-toggle text-white p-1 rounded-circle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: rgba(255,255,255,0.1); transition: all 0.3s ease;">
                                    <i class="bi bi-person-circle fs-4 align-middle"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="navbarDropdown" style="min-width: 220px;">
                                    <li class="dropdown-header">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle fs-5 me-2 text-primary"></i>
                                            <div>
                                                <div class="fw-semibold">{{ Auth::user()->nama_karyawan }}</div>
                                                <small class="text-muted">
                                                    @switch($activeRole)
                                                        @case('DOKTER')
                                                            Dokter
                                                            @break
                                                        @case('PASIEN')
                                                            Pasien
                                                            @break
                                                        @case('PENGADAAN')
                                                            Staff Pengadaan
                                                            @break
                                                        @default
                                                            Pengguna
                                                    @endswitch
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                                            <i class="bi bi-gear-fill me-2 text-primary"></i>Pengaturan Profil
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                            @csrf
                                            <a class="dropdown-item d-flex align-items-center text-danger" href="{{ route('logout') }}"
                                               onclick="event.preventDefault(); this.closest('form').submit();">
                                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    @endif
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
            const updateDateTime = () => {
                const now = new Date();
                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                const element = document.getElementById('current-datetime');
                if (element) {
                    element.textContent = now.toLocaleDateString('id-ID', options);
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                updateDateTime();
                setInterval(updateDateTime, 1000);

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
