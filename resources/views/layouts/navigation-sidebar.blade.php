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
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('daftar-penyakit.*') ? 'active' : '' }}" href="{{ route('daftar-penyakit.index') }}">
                        <i class="bi bi-clipboard-data-fill"></i> Daftar Penyakit
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
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('laporan.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#laporanSubmenu" 
                       aria-expanded="{{ request()->routeIs('laporan.*') ? 'true' : 'false' }}" aria-controls="laporanSubmenu">
                        <i class="bi bi-printer-fill"></i> 
                        Laporan
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('laporan.*') ? 'show' : '' }}" id="laporanSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('laporan.harian') ? 'active' : '' }}" href="{{ route('laporan.harian') }}">
                                    <i class="bi bi-calendar-day"></i> Laporan Harian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('laporan.pemakaian_obat') ? 'active' : '' }}" href="{{ route('laporan.pemakaian_obat') }}">
                                    <i class="bi bi-capsule"></i> Pemakaian Obat
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('laporan.index') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                    <i class="bi bi-file-pdf"></i> Cetak Laporan PDF
                                </a>
                            </li>
                        </ul>
                    </div>
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
                    <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('barang-medis.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('permintaan.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="collapse" data-bs-target="#obatAlatMedisSubmenu" 
                       aria-expanded="{{ request()->routeIs('barang-medis.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('permintaan.*') ? 'true' : 'false' }}" aria-controls="obatAlatMedisSubmenu">
                        <i class="bi bi-archive-fill"></i> 
                        Obat &amp; Alat Medis
                        <i class="bi bi-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('barang-medis.*') || request()->routeIs('barang-masuk.*') || request()->routeIs('permintaan.*') ? 'show' : '' }}" id="obatAlatMedisSubmenu">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('barang-medis.index') ? 'active' : '' }}" href="{{ route('barang-medis.index') }}">
                                    <i class="bi bi-grid-fill"></i> Daftar Obat & Alat Medis
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('permintaan.index') ? 'active' : '' }}" href="{{ route('permintaan.index') }}">
                                    <i class="bi bi-list-check"></i> Daftar Permintaan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('barang-masuk.index') ? 'active' : '' }}" href="{{ route('barang-masuk.index') }}">
                                    <i class="bi bi-clock-history"></i> Riwayat Barang Masuk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('barang-masuk.create') ? 'active' : '' }}" href="{{ route('barang-masuk.create') }}">
                                    <i class="bi bi-plus-circle-fill"></i> Input Barang Masuk
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-2 {{ request()->routeIs('barang-medis.create') ? 'active' : '' }}" href="{{ route('barang-medis.create') }}">
                                    <i class="bi bi-plus-square-fill"></i> Tambah Barang Baru
                                </a>
                            </li>
                        </ul>
                    </div>
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
