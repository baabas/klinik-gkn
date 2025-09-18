<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse shadow-sm">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">

            {{-- ============================================= --}}
            {{-- MENU UNTUK ROLE DOKTER --}}
            {{-- ============================================= --}}
            @if(Auth::user()->hasRole('DOKTER'))
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pasien.*') ? 'active' : '' }}" href="{{ route('pasien.index') }}">
                        <i class="bi bi-people-fill"></i> Daftar Pasien
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('barang-medis.*') ? 'active' : '' }}" href="{{ route('barang-medis.index') }}">
                        <i class="bi bi-archive-fill"></i> Obat & Alat Medis
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('permintaan.*') ? 'active' : '' }}" href="{{ route('permintaan.index') }}">
                        <i class="bi bi-file-earmark-text-fill"></i> Permintaan Barang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                        <i class="bi bi-printer-fill"></i> Cetak Laporan
                    </a>
                </li>
            @endif

            {{-- ============================================= --}}
            {{-- MENU UNTUK ROLE PENGADAAN --}}
            {{-- ============================================= --}}
            @if(Auth::user()->hasRole('PENGADAAN'))
                 <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('barang-medis.*') ? 'active' : '' }}" href="{{ route('barang-medis.index') }}">
                        <i class="bi bi-archive-fill"></i> Obat &amp; Alat Medis
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('permintaan.*') ? 'active' : '' }}" href="{{ route('permintaan.index') }}">
                        <i class="bi bi-inbox-fill"></i> Proses Permintaan
                    </a>
                </li>
                {{-- ITEM MENU 'DISTRIBUSI BARANG' YANG MENYEBABKAN ERROR TELAH DIHAPUS DARI SINI --}}
            @endif


            {{-- ============================================= --}}
            {{-- MENU UNTUK ROLE PASIEN --}}
            {{-- ============================================= --}}
            @if(Auth::user()->hasRole('PASIEN'))
                 <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('pasien.my_card') ? 'active' : '' }}" href="{{ route('pasien.my_card') }}">
                        <i class="bi bi-person-vcard-fill"></i>
                        Kartu Pasien Saya
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>
