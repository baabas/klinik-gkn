<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse shadow-sm">
    <div class="position-sticky pt-3">
        @if(Auth::user()->roles()->where('name', 'DOKTER')->exists())
            {{-- MENU UNTUK DOKTER --}}
            <ul class="nav flex-column">
                <li class="nav-item">
                    {{-- Kelas 'active' dari Bootstrap akan memberi highlight biru --}}
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ (request()->routeIs('pasien.index') || request()->routeIs('pasien.show')) ? 'active' : '' }}"
                       href="{{ route('pasien.index') }}">
                        <i class="bi bi-people-fill"></i>
                        Daftar Pasien
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('obat.*') ? 'active' : '' }}"
                       href="{{ route('obat.index') }}">
                        <i class="bi bi-capsule-pill"></i>
                        Stok Obat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="collapse" href="#laporanSubmenu" role="button">
                        <i class="bi bi-file-earmark-text-fill"></i>
                        Laporan
                    </a>
                    <div class="collapse {{ request()->routeIs('laporan.*') ? 'show' : '' }}" id="laporanSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('laporan.harian') ? 'active' : '' }}" href="{{ route('laporan.harian') }}">
                                    Laporan Harian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('laporan.pemakaian_obat') ? 'active' : '' }}" href="{{ route('laporan.pemakaian_obat') }}">
                                    Pemakaian Obat
                                </a>
                            </li>
                             <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('laporan.index') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                    Cetak PDF
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        @else
            {{-- MENU UNTUK PASIEN --}}
             <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pasien.my_card') ? 'active' : '' }}"
                       href="{{ route('pasien.my_card') }}">
                       <i class="bi bi-person-vcard-fill"></i>
                       Kartu Pasien Saya
                    </a>
                </li>
            </ul>
        @endif
    </div>
</nav>
