@php
    $showNavigation = $showNavigation ?? true;
    $showSidebarToggle = $showSidebarToggle ?? false;
@endphp

<nav class="navbar navbar-dark bg-dark fixed-top shadow-sm">
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

            @if($showNavigation && Auth::check() && Auth::user()->hasRole('PENGADAAN'))
            <ul class="navbar-nav flex-row align-items-center gap-2 ms-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('barang-medis.*') || request()->routeIs('permintaan.*') ? 'active' : '' }}"
                        href="{{ route('barang-medis.index') }}">
                        Obat & Alat Medis
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link px-2 {{ request()->routeIs('permintaan.*') ? 'active' : '' }}" href="{{ route('permintaan.index') }}">
                        Proses Permintaan
                    </a>
                </li>
            </ul>
        @endif

        <div class="d-flex align-items-center text-white ms-auto">
            @if(Auth::check())
                <span class="d-none d-sm-inline">Hallo, {{ Auth::user()->nama_karyawan }}</span>
                <div class="nav-item dropdown ms-3">
                    <a class="nav-link dropdown-toggle text-white p-0" href="#" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 align-middle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">Logout</a>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>
    </div>
</nav>
