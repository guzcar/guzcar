<nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary shadow-sm">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="{{ route('home') }}">
        <b>Guzcar</b>
    </a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                @if(auth()->user()->getFilamentAvatarUrl())
                    <img src="{{ auth()->user()->getFilamentAvatarUrl() }}" alt="Perfil" class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                @else
                    @php
                        $nameParts = explode(' ', auth()->user()->name);
                        $initials = '';
                        foreach ($nameParts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1)) . ' ';
                        }
                        $initials = rtrim($initials);
                    @endphp
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($initials) }}&background=09090b&color=ffffff" alt="Perfil" class="rounded-circle" width="32" height="32">
                @endif
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                {{--
                <li><a class="dropdown-item" href="#!">Settings</a></li>
                <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                <li><hr class="dropdown-divider" /></li>
                --}}
                <li>
                    <a class="dropdown-item" href="{{ route('user.edit') }}">
                        <i class="fa-solid fa-circle-user me-2 text-secondary"></i>
                        {{ auth()->user()->name }}
                    </a>
                </li>
                <li><hr class="dropdown-divider" /></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item" type="submit">
                            <i class="fa-solid fa-right-from-bracket me-2 text-secondary"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </li>
    </ul>
</nav>
