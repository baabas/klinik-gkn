@php
    $showNavigation = $showNavigation ?? true;
    $showSidebarToggle = $showSidebarToggle ?? false;
    $showPengadaanNavigation = $showPengadaanNavigation ?? true;
    $activeRole = $activeRole ?? session('active_role');

    if (!isset($activeRole) && Auth::check()) {
        $activeRole = Auth::user()->roles()->pluck('name')->first();
    }
@endphp

<nav class="navbar navbar-dark bg-dark fixed-top shadow-sm" style="z-index: 1050;">
    <div class="container-fluid d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            @if($showSidebarToggle)
                <button class="navbar-toggler d-lg-none me-2" type="button" data-bs-toggle="collapse"
                    data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
                    aria-label="Toggle sidebar">
                    <span class="navbar-toggler-icon"></span>
                </button>
            @endif

            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/logo-klinik.png') }}" alt="Logo Klinik" style="height: 30px;" class="me-2">
                Klinik GKN
            </a>
        </div>

        {{-- Navigation Menu for PENGADAAN role --}}
        @if($showNavigation && $showPengadaanNavigation && ($activeRole === 'PENGADAAN') && Auth::check() && Auth::user()->hasRole('PENGADAAN'))
            <div class="d-flex align-items-center">
                <ul class="navbar-nav flex-row align-items-center gap-1 ms-4">
                    {{-- Obat & Alat Medis Dropdown Menu --}}
                    <li class="nav-item dropdown">
                        <div class="d-flex align-items-center" id="obat-medis-nav">
                            <a class="nav-link text-white px-2 py-2 rounded {{ request()->routeIs('barang-medis.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('permintaan.*') ? 'bg-primary' : '' }}"
                               href="{{ route('barang-medis.index') }}"
                               style="transition: all 0.3s ease; font-weight: 500; text-decoration: none;">
                                <i class="bi bi-archive-fill me-2"></i>Obat & Alat Medis
                                @if(isset($pengadaanNotifications) && $pengadaanNotifications['total'] > 0)
                                    <span class="badge bg-danger rounded-pill ms-2">{{ $pengadaanNotifications['total'] }}</span>
                                @endif
                            </a>
                            <button class="btn btn-sm text-white p-1 ms-1" type="button" id="obat-medis-dropdown" aria-expanded="false"
                                    style="background: none; border: none; font-size: 0.8rem;">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                        <ul class="dropdown-menu shadow-lg border-0" id="obat-medis-menu" style="min-width: 220px; display: none;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('barang-medis.index') ? 'active' : '' }}"
                                   href="{{ route('barang-medis.index') }}">
                                    <i class="bi bi-grid-fill me-2 text-primary"></i>
                                    Daftar Obat & Alat Medis
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ request()->routeIs('permintaan.index') ? 'active' : '' }}"
                                   href="{{ route('permintaan.index') }}">
                                    <span>
                                        <i class="bi bi-list-check me-2 text-success"></i>
                                        Daftar Permintaan
                                    </span>
                                    @if(isset($pengadaanNotifications) && $pengadaanNotifications['pending_requests'] > 0)
                                        <span class="badge bg-danger rounded-pill">{{ $pengadaanNotifications['pending_requests'] }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('barang-masuk.index') ? 'active' : '' }}"
                                   href="{{ route('barang-masuk.index') }}">
                                    <i class="bi bi-clock-history me-2 text-info"></i>
                                    Riwayat Barang Masuk
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ request()->routeIs('barang-masuk.create') ? 'active' : '' }}"
                                   href="{{ route('barang-masuk.create') }}">
                                    <span>
                                        <i class="bi bi-plus-circle-fill me-2 text-warning"></i>
                                        Input Barang Masuk
                                    </span>
                                    @if(isset($pengadaanNotifications) && $pengadaanNotifications['approved_for_input'] > 0)
                                        <span class="badge bg-warning text-dark rounded-pill">{{ $pengadaanNotifications['approved_for_input'] }}</span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center justify-content-between {{ request()->routeIs('barang-medis.create') ? 'active' : '' }}"
                                   href="{{ route('barang-medis.create') }}">
                                    <span>
                                        <i class="bi bi-plus-square-fill me-2 text-danger"></i>
                                        Tambah Barang Baru
                                    </span>
                                    @if(isset($pengadaanNotifications) && $pengadaanNotifications['new_items_to_add'] > 0)
                                        <span class="badge bg-info rounded-pill">{{ $pengadaanNotifications['new_items_to_add'] }}</span>
                                    @endif
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Master Data Dropdown Menu --}}
                    <li class="nav-item dropdown">
                        <div class="d-flex align-items-center" id="master-data-nav">
                            <a class="nav-link text-white px-2 py-2 rounded {{ request()->routeIs('master-kantor.*') || request()->routeIs('master-isi-kemasan.*') || request()->routeIs('master-satuan.*') || request()->routeIs('master-whatsapp-validator.*') ? 'bg-primary' : '' }}"
                               href="{{ route('master-kantor.index') }}"
                               style="transition: all 0.3s ease; font-weight: 500; text-decoration: none;">
                                <i class="bi bi-database-fill me-2"></i>Master Data
                            </a>
                            <button class="btn btn-sm text-white p-1 ms-1" type="button" id="master-data-dropdown" aria-expanded="false"
                                    style="background: none; border: none; font-size: 0.8rem;">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                        <ul class="dropdown-menu shadow-lg border-0" id="master-data-menu" style="min-width: 220px; display: none;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('master-kantor.*') ? 'active' : '' }}"
                                   href="{{ route('master-kantor.index') }}">
                                    <i class="bi bi-building me-2 text-primary"></i>
                                    Master Kantor
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('master-isi-kemasan.*') ? 'active' : '' }}"
                                   href="{{ route('master-isi-kemasan.index') }}">
                                    <i class="bi bi-box-seam me-2 text-success"></i>
                                    Master Isi Kemasan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('master-satuan.*') ? 'active' : '' }}"
                                   href="{{ route('master-satuan.index') }}">
                                    <i class="bi bi-rulers me-2 text-info"></i>
                                    Master Satuan Terkecil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center {{ request()->routeIs('master-whatsapp-validator.*') ? 'active' : '' }}"
                                   href="{{ route('master-whatsapp-validator.index') }}">
                                    <i class="bi bi-whatsapp me-2 text-success"></i>
                                    Master WA Validator
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Log Distribusi Menu --}}
                    <li class="nav-item">
                        <a class="nav-link text-white px-3 py-2 rounded {{ request()->routeIs('distribusi-barang.*') ? 'bg-primary' : '' }}"
                           href="{{ route('distribusi-barang.index') }}"
                           style="transition: all 0.3s ease; font-weight: 500; text-decoration: none;">
                            <i class="bi bi-arrow-left-right me-2"></i>Log Distribusi
                        </a>
                    </li>
                </ul>
            </div>
        @endif

        {{-- Page Title/Breadcrumb Section - Only for roles without top navigation --}}
        @if($showNavigation && Auth::check() && $activeRole === 'DOKTER' && Auth::user()->hasRole('DOKTER'))
            <div class="d-flex align-items-center text-white flex-grow-1">
                <div class="ms-4">
                    @if(request()->routeIs('dashboard'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            Dokter
                        </span>
                    @elseif(request()->routeIs('barang-medis.*'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-archive-fill me-2"></i>Obat & Alat Medis
                        </span>
                    @elseif(request()->routeIs('permintaan.*'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-list-check me-2"></i>Permintaan Barang
                        </span>
                    @elseif(request()->routeIs('barang-masuk.*'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-clock-history me-2"></i>Barang Masuk
                        </span>
                    @elseif(request()->routeIs('daftar-penyakit.*'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-clipboard-data-fill me-2"></i>Daftar Penyakit
                        </span>
                    @elseif(request()->routeIs('pasien.*'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-people-fill me-2"></i>Daftar Pasien
                        </span>
                    @elseif(request()->routeIs('rekam-medis.*'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-clipboard-pulse me-2"></i>Rekam Medis
                        </span>
                    @elseif(request()->routeIs('laporan.*'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-printer-fill me-2"></i>Laporan
                        </span>
                    @endif
                </div>
            </div>
        @endif

        <div class="d-flex align-items-center text-white ms-auto">
            {{-- Current Date and Time --}}
            <div class="d-none d-md-flex flex-column align-items-end me-3">
                <small class="text-light opacity-75" id="current-datetime">
                    {{ date('d M Y, H:i') }}
                </small>
            </div>

            @if(Auth::check())
                {{-- Profile Dropdown dengan nama user --}}
                <div class="nav-item dropdown me-2" style="position: relative;">
                    <a class="nav-link text-white px-3 py-2 d-flex align-items-center" 
                       href="javascript:void(0)" 
                       id="userDropdown" 
                       role="button" 
                       aria-expanded="false" 
                       style="background-color: rgba(255,255,255,0.1); transition: all 0.3s ease; border-radius: 8px;">
                        <i class="bi bi-person-circle fs-5 me-2"></i>
                        <span class="d-none d-sm-inline">Hallo, {{ Auth::user()->nama_karyawan }}</span>
                        <i class="bi bi-chevron-down ms-2 small"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown" style="min-width: 250px;">
                        <li class="dropdown-header bg-light">
                            <div class="d-flex align-items-center py-2">
                                <i class="bi bi-person-circle fs-3 me-3 text-primary"></i>
                                <div>
                                    <div class="fw-bold text-dark">{{ Auth::user()->nama_karyawan }}</div>
                                    <small class="text-muted">{{ Auth::user()->email }}</small>
                                    <br>
                                    <small class="badge bg-primary mt-1">
                                        @switch($activeRole)
                                            @case('PENGADAAN')
                                                Staff Pengadaan
                                                @break
                                            @case('DOKTER')
                                                Dokter
                                                @break
                                            @case('PASIEN')
                                                Pasien
                                                @break
                                            @default
                                                User
                                        @endswitch
                                    </small>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider my-2"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center py-2" href="{{ route('profile.edit') }}" style="transition: all 0.2s;">
                                <i class="bi bi-gear-fill me-3 text-primary fs-5"></i>
                                <div>
                                    <div class="fw-semibold">Pengaturan Profile</div>
                                    <small class="text-muted">Update informasi akun Anda</small>
                                </div>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-2"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="px-3 py-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-box-arrow-right me-2"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</nav>

{{-- Enhanced Top Navigation Styles --}}
<style>
    .navbar-brand {
        font-weight: 600;
        font-size: 1.25rem;
        transition: all 0.3s ease;
    }
    .navbar-brand:hover {
        transform: scale(1.02);
    }
    .navbar-text {
        font-weight: 500;
        font-size: 0.95rem;
        opacity: 0.9;
    }
    .navbar .dropdown-toggle::after {
        display: none;
    }
    .navbar .nav-link:hover {
        color: #fff !important;
        opacity: 0.8;
    }

    /* Top Navigation Dropdown Styles */
    .navbar-nav .nav-link {
        font-size: 0.9rem;
        font-weight: 500;
        border-radius: 6px;
    }

    .navbar-nav .nav-link:hover {
        background-color: rgba(255,255,255,0.1) !important;
        transform: translateY(-1px);
    }

    .navbar-nav .nav-link.bg-primary {
        background-color: #0d6efd !important;
    }

    .navbar-nav .dropdown-menu {
        border: none;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        z-index: 9998;
        background-color: rgba(255,255,255,0.98);
        backdrop-filter: blur(10px);
        min-width: 220px;
    }

    /* Differentiate between navigation dropdown and user profile dropdown */
    .navbar-nav .dropdown-menu::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 20px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid rgba(255,255,255,0.98);
        z-index: 9999;
    }

    .navbar-nav .dropdown-item {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        border-radius: 6px;
        margin: 0 0.5rem;
    }

    .navbar-nav .dropdown-item:hover {
        background-color: rgba(13, 110, 253, 0.1);
        transform: translateX(3px);
    }

    .navbar-nav .dropdown-item.active {
        background-color: rgba(13, 110, 253, 0.15);
        color: #0d6efd;
        font-weight: 600;
    }

    /* Split navigation button styles */
    .navbar-nav .nav-item .d-flex {
        border-radius: 6px;
        overflow: hidden;
        background-color: rgba(255,255,255,0.05);
        transition: all 0.3s ease;
    }

    .navbar-nav .nav-item .d-flex:hover {
        background-color: rgba(255,255,255,0.1);
        transform: translateY(-1px);
    }

    .navbar-nav .nav-item .d-flex .btn:hover {
        background-color: rgba(255,255,255,0.1) !important;
    }

    .navbar-nav .nav-item .d-flex .btn:focus {
        box-shadow: none !important;
        outline: none !important;
    }

    /* Specific styling for Obat & Alat Medis dropdown */
    #obat-medis-nav {
        position: relative;
    }

    #obat-medis-menu {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        z-index: 9999 !important;
        min-width: 220px;
        background-color: rgba(255,255,255,0.98) !important;
        backdrop-filter: blur(10px);
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        padding: 0.5rem 0;
        margin-top: 0.5rem;
    }

    #obat-medis-menu::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 20px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid rgba(255,255,255,0.98);
        z-index: 10000;
    }

    /* Specific styling for Master Data dropdown */
    #master-data-nav {
        position: relative;
    }

    #master-data-menu {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        z-index: 9999 !important;
        min-width: 220px;
        background-color: rgba(255,255,255,0.98) !important;
        backdrop-filter: blur(10px);
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        padding: 0.5rem 0;
        margin-top: 0.5rem;
    }

    #master-data-menu::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 20px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid rgba(255,255,255,0.98);
        z-index: 10000;
    }
    #current-datetime {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        line-height: 1.2;
    }

    /* Enhanced dropdown styles */
    .dropdown-menu {
        border-radius: 10px;
        padding: 0.5rem 0;
        animation: fadeInDown 0.3s ease-out;
        z-index: 9999 !important;
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
        border: 1px solid rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
        background-color: rgba(255,255,255,0.98) !important;
    }

    .dropdown-item {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        border-radius: 6px;
        margin: 0 0.5rem;
    }

    .dropdown-item:hover {
        background-color: rgba(13, 110, 253, 0.1);
        transform: translateX(2px);
    }

    .dropdown-header {
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        margin: -0.5rem 0 0.5rem 0;
        border-radius: 10px 10px 0 0;
    }

    /* Profile icon hover effect */
    .nav-item.dropdown > a:hover {
        background-color: rgba(255,255,255,0.2) !important;
        transform: scale(1.05);
    }

    /* Fix dropdown positioning and z-index issues */
    .navbar {
        z-index: 1050 !important;
    }

    .nav-item.dropdown {
        position: relative !important;
    }

    .dropdown-menu.show {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        z-index: 10000 !important;
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
        margin-top: 0.125rem !important;
    }

    .dropdown-menu {
        display: none;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.2s ease, visibility 0.2s ease;
    }

    /* User profile dropdown specific styles */
    #userDropdown {
        cursor: pointer;
    }

    #userDropdown:hover {
        background-color: rgba(255,255,255,0.2) !important;
    }

    .dropdown-item:hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    /* Ensure dropdown appears above all content */
    .dropdown-menu::before {
        content: '';
        position: absolute;
        top: -6px;
        right: 15px;
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid rgba(255,255,255,0.98);
        z-index: 10001;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive improvements */
    /* Fix any container overflow issues */
    .container-fluid {
        overflow: visible !important;
        position: relative;
    }

    body {
        overflow-x: hidden;
        overflow-y: auto;
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .navbar-text {
            font-size: 0.85rem;
        }
        #current-datetime {
            display: none !important;
        }
        .dropdown-menu {
            min-width: 180px !important;
            right: 0 !important;
            left: auto !important;
        }
    }
</style>

{{-- Real-time Clock Script --}}
<script>
    function updateDateTime() {
        const now = new Date();
        const options = {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        const dateTimeString = now.toLocaleDateString('id-ID', options);
        const element = document.getElementById('current-datetime');
        if (element) {
            element.textContent = dateTimeString;
        }
    }

    // Update immediately and then every second
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Fix dropdown positioning issues
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.querySelector('.nav-item.dropdown .dropdown-toggle');
        const dropdownMenu = document.querySelector('.nav-item.dropdown .dropdown-menu');

        if (dropdownToggle && dropdownMenu) {
            dropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Close obat medis dropdown if open
                const obatMedisMenu = document.getElementById('obat-medis-menu');
                const obatMedisButton = document.getElementById('obat-medis-dropdown');
                if (obatMedisMenu) {
                    obatMedisMenu.style.display = 'none';
                    if (obatMedisButton) {
                        obatMedisButton.setAttribute('aria-expanded', 'false');
                    }
                }

                // Toggle dropdown manually with proper positioning
                const isOpen = dropdownMenu.classList.contains('show');

                if (isOpen) {
                    dropdownMenu.classList.remove('show');
                } else {
                    // Ensure proper positioning
                    dropdownMenu.style.position = 'absolute';
                    dropdownMenu.style.top = '100%';
                    dropdownMenu.style.right = '0';
                    dropdownMenu.style.left = 'auto';
                    dropdownMenu.style.zIndex = '10000';
                    dropdownMenu.style.marginTop = '0.125rem';

                    dropdownMenu.classList.add('show');
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            });

            // Prevent dropdown from closing when clicking inside it
            dropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Handle Obat & Alat Medis dropdown separately
        const obatMedisButton = document.getElementById('obat-medis-dropdown');
        const obatMedisMenu = document.getElementById('obat-medis-menu');

        if (obatMedisButton && obatMedisMenu) {
            obatMedisButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Close user profile dropdown if open
                const userDropdown = document.querySelector('.nav-item.dropdown .dropdown-menu');
                if (userDropdown) {
                    userDropdown.classList.remove('show');
                }

                // Close Master Data dropdown if open
                const masterDataMenu = document.getElementById('master-data-menu');
                if (masterDataMenu) {
                    masterDataMenu.style.display = 'none';
                    const masterDataButton = document.getElementById('master-data-dropdown');
                    if (masterDataButton) {
                        masterDataButton.setAttribute('aria-expanded', 'false');
                    }
                }

                // Toggle obat medis dropdown
                const isOpen = obatMedisMenu.style.display === 'block';

                if (isOpen) {
                    obatMedisMenu.style.display = 'none';
                    obatMedisButton.setAttribute('aria-expanded', 'false');
                } else {
                    obatMedisMenu.style.display = 'block';
                    obatMedisMenu.style.position = 'absolute';
                    obatMedisMenu.style.top = '100%';
                    obatMedisMenu.style.left = '0';
                    obatMedisMenu.style.zIndex = '9999';
                    obatMedisButton.setAttribute('aria-expanded', 'true');
                }
            });

            // Close obat medis dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!obatMedisButton.contains(e.target) && !obatMedisMenu.contains(e.target)) {
                    obatMedisMenu.style.display = 'none';
                    obatMedisButton.setAttribute('aria-expanded', 'false');
                }
            });

            // Prevent dropdown from closing when clicking inside it
            obatMedisMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Handle Master Data dropdown
        const masterDataButton = document.getElementById('master-data-dropdown');
        const masterDataMenu = document.getElementById('master-data-menu');

        if (masterDataButton && masterDataMenu) {
            masterDataButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Close user profile dropdown if open
                const userDropdown = document.querySelector('.nav-item.dropdown .dropdown-menu');
                if (userDropdown) {
                    userDropdown.classList.remove('show');
                }

                // Close Obat Medis dropdown if open
                const obatMedisMenu = document.getElementById('obat-medis-menu');
                if (obatMedisMenu) {
                    obatMedisMenu.style.display = 'none';
                    const obatMedisButton = document.getElementById('obat-medis-dropdown');
                    if (obatMedisButton) {
                        obatMedisButton.setAttribute('aria-expanded', 'false');
                    }
                }

                // Toggle master data dropdown
                const isOpen = masterDataMenu.style.display === 'block';

                if (isOpen) {
                    masterDataMenu.style.display = 'none';
                    masterDataButton.setAttribute('aria-expanded', 'false');
                } else {
                    masterDataMenu.style.display = 'block';
                    masterDataMenu.style.position = 'absolute';
                    masterDataMenu.style.top = '100%';
                    masterDataMenu.style.left = '0';
                    masterDataMenu.style.zIndex = '9999';
                    masterDataButton.setAttribute('aria-expanded', 'true');
                }
            });

            // Close master data dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!masterDataButton.contains(e.target) && !masterDataMenu.contains(e.target)) {
                    masterDataMenu.style.display = 'none';
                    masterDataButton.setAttribute('aria-expanded', 'false');
                }
            });

            // Prevent dropdown from closing when clicking inside it
            masterDataMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Handle User Profile Dropdown - ALWAYS INITIALIZE
        const userDropdownToggle = document.getElementById('userDropdown');
        const userDropdownMenu = userDropdownToggle ? userDropdownToggle.nextElementSibling : null;

        console.log('User Dropdown Elements:', {
            toggle: userDropdownToggle,
            menu: userDropdownMenu
        });

        if (userDropdownToggle && userDropdownMenu) {
            // Remove any existing listeners to prevent duplicates
            const newToggle = userDropdownToggle.cloneNode(true);
            userDropdownToggle.parentNode.replaceChild(newToggle, userDropdownToggle);
            
            const refreshedToggle = document.getElementById('userDropdown');
            const refreshedMenu = refreshedToggle.nextElementSibling;

            refreshedToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('User dropdown clicked!');

                // Close obat medis dropdown if open
                if (obatMedisMenu && obatMedisMenu.style) {
                    obatMedisMenu.style.display = 'none';
                }

                // Close master data dropdown if open
                const masterDataMenu = document.getElementById('master-data-menu');
                if (masterDataMenu && masterDataMenu.style) {
                    masterDataMenu.style.display = 'none';
                }

                // Toggle user dropdown
                const isOpen = refreshedMenu.classList.contains('show');

                // Close all dropdowns first
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu !== refreshedMenu) {
                        menu.classList.remove('show');
                    }
                });

                if (!isOpen) {
                    refreshedMenu.classList.add('show');
                    refreshedToggle.setAttribute('aria-expanded', 'true');
                    console.log('User dropdown opened');
                } else {
                    refreshedMenu.classList.remove('show');
                    refreshedToggle.setAttribute('aria-expanded', 'false');
                    console.log('User dropdown closed');
                }
            });

            // Close user dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (refreshedMenu && !refreshedToggle.contains(e.target) && !refreshedMenu.contains(e.target)) {
                    refreshedMenu.classList.remove('show');
                    refreshedToggle.setAttribute('aria-expanded', 'false');
                }
            });
        } else {
            console.error('User dropdown elements not found!');
        }
    });
</script>
