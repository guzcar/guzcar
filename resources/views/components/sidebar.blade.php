<nav class="sb-sidenav accordion sb-sidenav-light shadow-sm" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading py-2"></div>
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>
                Inicio
            </a>
            <a class="nav-link {{ request()->routeIs('trabajos.asignar') ? 'active' : '' }}" href="{{ route('trabajos.asignar') }}">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-car-side"></i></div>
                Vehículos
            </a>
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small text-secondary">Sesión iniciada como:</div>
        {{ auth()->user()->name }}
    </div>
</nav>
