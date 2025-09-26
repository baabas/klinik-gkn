@php
    $showNavigation = $showNavigation ?? true;
    $showSidebarToggle = $showSidebarToggle ?? false;
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
        @if($showNavigation && Auth::check() && Auth::user()->hasRole('PENGADAAN'))
            <div class="d-flex align-items-center">
                <ul class="navbar-nav flex-row align-items-center gap-1 ms-4">
                    {{-- Dashboard Menu --}}
                    <li class="nav-item">
                        <a class="nav-link text-white px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-primary' : '' }}" 
                           href="{{ route('dashboard') }}"
                           style="transition: all 0.3s ease; font-weight: 500; text-decoration: none;">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard Pengadaan
                        </a>
                    </li>
                    
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
                </ul>
            </div>
        @endif

        {{-- Page Title/Breadcrumb Section - Only for roles without top navigation --}}
        @if($showNavigation && Auth::check() && !Auth::user()->hasRole('PENGADAAN'))
            <div class="d-flex align-items-center text-white flex-grow-1">
                <div class="ms-4">
                    @if(request()->routeIs('dashboard'))
                        <span class="navbar-text text-light">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            @if(Auth::user()->hasRole('DOKTER'))
                                Dokter
                            @endif
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
                <span class="d-none d-sm-inline me-3">Hallo, {{ Auth::user()->nama_karyawan }}</span>
                
                {{-- Profile Dropdown (tanpa logout) --}}
                <div class="nav-item dropdown me-2" style="position: relative;">
                    <a class="nav-link dropdown-toggle text-white p-1 rounded-circle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false" style="background-color: rgba(255,255,255,0.1); transition: all 0.3s ease;">
                        <i class="bi bi-person-circle fs-4 align-middle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" aria-labelledby="userDropdown" style="min-width: 200px;">
                        <li class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-circle fs-5 me-2 text-primary"></i>
                                <div>
                                    <div class="fw-semibold">{{ Auth::user()->nama_karyawan }}</div>
                                    <small class="text-muted">
                                        @if(Auth::user()->hasRole('PENGADAAN'))
                                            Staff Pengadaan
                                        @elseif(Auth::user()->hasRole('DOKTER'))
                                            Dokter
                                        @elseif(Auth::user()->hasRole('PASIEN'))
                                            Pasien
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.edit') }}">
                                <i class="bi bi-gear me-2 text-secondary"></i>
                                Profile Settings
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Tombol Logout Terpisah --}}
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm d-flex align-items-center" 
                            style="border-color: rgba(255,255,255,0.3); transition: all 0.3s ease;"
                            onmouseover="this.style.backgroundColor='rgba(220,53,69,0.1)'; this.style.borderColor='#dc3545'; this.style.color='#dc3545';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='rgba(255,255,255,0.3)'; this.style.color='white';"
                            title="Logout dari sistem">
                        <i class="bi bi-box-arrow-right me-1"></i>
                        <span class="d-none d-sm-inline">Logout</span>
                    </button>
                </form>
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
        z-index: 10000 !important;
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        transform: none !important;
        margin-top: 0.125rem !important;
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
    });
</script>
