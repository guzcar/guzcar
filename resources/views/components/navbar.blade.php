<nav class="sb-topnav navbar navbar-expand navbar-dark bg-primary shadow-sm">
    <a class="navbar-brand ps-3" href="{{ route('home') }}">
        <b>Guzcar</b>
    </a>
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
        <i class="fas fa-bars"></i>
    </button>

    <ul class="navbar-nav ms-auto me-3 me-lg-4">
        {{-- INICIO: Ícono de Campana / Notificaciones --}}
        <li class="nav-item dropdown me-3">
            <a class="nav-link dropdown-toggle position-relative" id="navbarDropdownNotifications" href="#"
                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell fa-lg"></i>
                @if(isset($globalEvents) && $globalEvents->count() > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 0.6rem;">
                        {{ $globalEvents->count() }}
                        <span class="visually-hidden">notificaciones</span>
                    </span>
                @endif
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow p-0" aria-labelledby="navbarDropdownNotifications"
                style="width: 320px; max-height: 400px; overflow-y: auto;">
                <li
                    class="dropdown-header bg-light fw-bold border-bottom d-flex justify-content-between align-items-center">
                    <span>Avisos Recientes</span>
                    <span class="badge bg-secondary">{{ $globalEvents->count() ?? 0 }}</span>
                </li>

                @if(isset($globalEvents) && $globalEvents->count() > 0)
                    @foreach($globalEvents as $event)
                        <li>
                            <div class="dropdown-item p-3 border-bottom" style="white-space: normal; cursor: pointer;"
                                data-bs-toggle="modal" data-bs-target="#globalEventModal" onclick="loadModalContent(
                                        '{{ addslashes($event->title) }}', 
                                        '{{ addslashes(strip_tags($event->description)) }}', 
                                        '{{ $event->ends_at->format('d/m/Y H:i') }}'
                                    )">

                                <h6 class="mb-1 text-primary fw-bold text-truncate">{{ $event->title }}</h6>
                                <p class="mb-1 small text-muted">
                                    {{ Str::limit(strip_tags($event->description) ?: 'Sin detalles adicionales.', 60) }}
                                </p>
                                <small class="text-secondary" style="font-size: 0.75rem;">
                                    <i class="far fa-clock me-1"></i> Vence: {{ $event->ends_at->format('d/m H:i') }}
                                </small>
                            </div>
                        </li>
                    @endforeach
                @else
                    <li>
                        <div class="dropdown-item text-muted text-center py-4">No hay avisos nuevos</div>
                    </li>
                @endif
            </ul>
        </li>
        {{-- FIN: Ícono de Campana --}}

        {{-- INICIO: Menú de Usuario --}}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle p-0" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                @if(auth()->user()->getFilamentAvatarUrl())
                    <img src="{{ auth()->user()->getFilamentAvatarUrl() }}" alt="Perfil" class="rounded-circle" width="32"
                        height="32" style="object-fit: cover;">
                @else
                    @php
                        $nameParts = explode(' ', auth()->user()->name);
                        $initials = '';
                        foreach ($nameParts as $part) {
                            $initials .= strtoupper(substr($part, 0, 1));
                        }
                        $initials = substr($initials, 0, 2); // Limitar a 2 iniciales
                    @endphp
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($initials) }}&background=09090b&color=ffffff"
                        alt="Perfil" class="rounded-circle" width="32" height="32">
                @endif
            </a>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item" href="{{ route('user.edit') }}">
                        <i class="fa-solid fa-circle-user fa-fw me-2 text-muted"></i>
                        {{ auth()->user()->name }}
                    </a>
                </li>

                @if (auth()->user()->is_admin)
                    <li>
                        <a class="dropdown-item" href="{{ route('filament.admin.pages.dashboard') }}">
                            <i class="fa-solid fa-lock fa-fw me-2 text-muted"></i>
                            Módulo Administrativo
                        </a>
                    </li>
                @endif

                <li>
                    <a class="dropdown-item" href="{{ asset('docs/manual-taller.pdf') }}" target="_blank">
                        <i class="fa-solid fa-file fa-fw me-2 text-muted"></i>
                        Manual de Usuario
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider" />
                </li>

                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item" type="submit">
                            <i class="fa-solid fa-right-from-bracket fa-fw me-2 text-muted"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </li>
        {{-- FIN: Menú de Usuario --}}
    </ul>
</nav>

{{-- MODAL ÚNICO Y DINÁMICO --}}
<div class="modal fade" id="globalEventModal" tabindex="-1" aria-labelledby="globalEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="globalEventModalLabel">
                    <i class="fas fa-bullhorn me-2"></i> Aviso Importante
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <h4 class="text-dark fw-bold mb-3" id="modalTitle"></h4>
                <div class="text-muted mb-4" id="modalDescription" style="white-space: pre-wrap;"></div>

                <div class="alert alert-light border d-flex align-items-center" role="alert">
                    <i class="fas fa-clock text-primary me-2"></i>
                    <div>Vigente hasta: <strong id="modalDate"></strong></div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if(isset($modalEvent) && $modalEvent)

            // Usamos ID del evento + la ETAPA ('first' o 'second')
            var eventUniqueKey = "{{ $modalEvent->id }}_{{ $modalEvent->notification_stage }}";
            var storageKey = 'guzcar_seen_' + eventUniqueKey;

            if (!sessionStorage.getItem(storageKey)) {
                // Cargar contenido
                document.getElementById('modalTitle').innerText = "{{ $modalEvent->title }}";

                // Agregamos un prefijo si es el segundo aviso para dar urgencia
                var desc = `{{ $modalEvent->description ?? '' }}`;
                @if($modalEvent->notification_stage === 'second')
                    desc = "⚠️ SEGUNDO AVISO: \n" + desc;
                @endif

                document.getElementById('modalDescription').innerText = desc;
                document.getElementById('modalDate').innerText = "{{ $modalEvent->ends_at->format('d/m/Y H:i') }}";

                var myModal = new bootstrap.Modal(document.getElementById('globalEventModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                myModal.show();

                document.getElementById('globalEventModal').addEventListener('hidden.bs.modal', function () {
                    sessionStorage.setItem(storageKey, 'true');
                });
            }
        @endif
    });
</script>