<nav class="sb-sidenav accordion sb-sidenav-light shadow-sm bg-white" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">

            <div class="sb-sidenav-menu-heading">INICIO</div>

            <a class="nav-link {{ request()->routeIs('home') || request()->routeIs('gestion.*') ? 'active bg-light' : '' }}" href="{{ route('home') }}">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-house fa-fw me-2"></i></div>
                Vehículos
            </a>
            <a class="nav-link {{ request()->routeIs('trabajos.asignar') ? 'active bg-light' : '' }}" href="{{ route('trabajos.asignar') }}">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-car-side fa-fw me-2"></i></div>
                Asignar vehículo
            </a>

            <div class="sb-sidenav-menu-heading">HISTORIAL</div>

            <a class="nav-link {{ request()->routeIs('articulos') ? 'active bg-light' : '' }}" href="{{ route('articulos') }}">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-box-archive fa-fw me-2"></i></div>
                Artículos
            </a>
            <a class="nav-link {{ request()->routeIs('consulta.*') ? 'active bg-light' : '' }}" href="{{ route('consulta.vehicular') }}">
                <div class="sb-nav-link-icon"><i class="fa-solid fa-magnifying-glass fa-fw me-2"></i></div>
                Consulta vehicular
            </a>

            <!-- <div class="sb-sidenav-menu-heading">HERRAMIENTAS</div>
            <a class="nav-link {{ request()->routeIs('asistencia.*') ? 'active bg-light' : '' }}" href="{{ route('asistencia.index') }}">
                <div class="sb-nav-link-icon"><i class="fas fa-fingerprint me-2"></i></div>
                Asistencia
            </a> -->
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small text-secondary">Sesión iniciada como:</div>
        {{ auth()->user()->name }}
    </div>
</nav>
