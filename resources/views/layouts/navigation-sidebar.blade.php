<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse shadow-sm">
    <div class="position-sticky pt-3">

        @if(Auth::user()->hasRole('DOKTER') || Auth::user()->hasRole('PENGADAAN'))

            {{-- MENU UNTUK ROLE ADMIN (DOKTER & PENGADAAN) --}}
            <ul class="nav flex-column">

                {{-- 1. MENU BERSAMA (Bisa dilihat Dokter & Pengadaan) --}}
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('barang-medis.*') ? 'active' : '' }}" href="{{ route('barang-medis.index') }}">
                        <i class="bi bi-archive-fill"></i>
                        Obat & Alat Medis
                    </a>
                </li>

                {{-- 2. MENU KHUSUS DOKTER --}}
                @if(Auth::user()->hasRole('DOKTER'))
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 {{ (request()->routeIs('pasien.index') || request()->routeIs('pasien.show')) ? 'active' : '' }}" href="{{ route('pasien.index') }}">
                            <i class="bi bi-people-fill"></i>
                            Daftar Pasien
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="collapse" href="#laporanSubmenu" role="button">
                            <i class="bi bi-file-earmark-text-fill"></i>
                            Laporan
                        </a>
                        <div class="collapse {{ request()->routeIs('laporan.*') ? 'show' : '' }}" id="laporanSubmenu">
                            <ul class="nav flex-column ms-3">
                                {{-- ... (isi submenu laporan Anda) ... --}}
                            </ul>
                        </div>
                    </li>
                @endif

                {{-- 3. MENU KHUSUS PENGADAAN --}}
                @if(Auth::user()->hasRole('PENGADAAN'))
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('distribusi.*') ? 'active' : '' }}" href="{{ route('distribusi.index') }}">
                            <i class="bi bi-truck"></i>
                            Distribusi Barang
                        </a>
                    </li>
                @endif
            </ul>

        @else
            {{-- MENU UNTUK PASIEN --}}
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pasien.my_card') ? 'active' : '' }}" href="{{ route('pasien.my_card') }}">
                       <i class="bi bi-person-vcard-fill"></i>
                       Kartu Pasien Saya
                    </a>
                </li>
            </ul>
        @endif
    </div>
</nav>
