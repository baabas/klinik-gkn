<nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
            <img src="{{ asset('images/logo-klinik.png') }}" alt="Logo Klinik" style="height: 30px;" class="me-2">
            Klinik GKN
        </a>
        <div class="d-flex align-items-center text-white">
            <span>Hallo, {{ Auth::user()->nama_karyawan }}</span>
            <div class="nav-item dropdown ms-3">
                <a class="nav-link dropdown-toggle text-white p-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"></a>
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
        </div>
    </div>
</nav>
