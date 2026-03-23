<x-layout>
    <div style="max-width: 1000px;" class="mx-auto pb-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-dark fw-bold mb-0">Detalles del Trabajo</h2>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i> Volver
            </a>
        </div>

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <strong>Placa:</strong> {{ $trabajo->vehiculo->placa }}
                    @if($trabajo->vehiculo->marca || $trabajo->vehiculo->modelo)
                        <span class="ms-3 small opacity-75">
                            {{ optional($trabajo->vehiculo->marca)->nombre ?? '' }}
                            {{ optional($trabajo->vehiculo->modelo)->nombre ?? '' }}
                        </span>
                    @endif
                </div>
                <span class="badge bg-light text-dark px-3 py-2">
                    {{ $trabajo->fecha_salida ? 'FINALIZADO' : 'EN TALLER' }}
                </span>
            </div>
            <div class="card-body p-4">

                <!-- Información adicional compacta -->
                <div class="row mb-4 pb-3 border-bottom">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Ingreso</small>
                        <strong>{{ $trabajo->fecha_ingreso?->format('d/m/Y') ?? 'N/A' }}</strong>
                        <span class="text-muted ms-2">{{ $trabajo->fecha_ingreso?->format('h:i A') }}</span>
                    </div>
                    @if($trabajo->fecha_salida)
                        <div class="col-md-4">
                            <small class="text-muted d-block">Salida</small>
                            <strong>{{ $trabajo->fecha_salida->format('d/m/Y') }}</strong>
                            <span class="text-muted ms-2">{{ $trabajo->fecha_salida->format('h:i A') }}</span>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <small class="text-muted d-block">Kilometraje</small>
                        <strong>{{ $trabajo->kilometraje ? number_format($trabajo->kilometraje, 0, ',', '.') . ' km' : 'No registrado' }}</strong>
                    </div>
                </div>

                <div class="mb-4 pb-2 border-bottom">
                    <h6 class="text-muted mb-2">Descripción del Servicio</h6>
                    <p class="mb-0">{{ $trabajo->descripcion_servicio ?? 'Sin descripción registrada.' }}</p>
                </div>

                <div class="row g-4">
                    <!-- Columna Izquierda: Artículos -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3"><i class="fa-solid fa-boxes me-2"></i> Artículos</h6>
                        <div class="bg-light rounded p-3">
                            @php
                                $articulosFiltrados = $trabajo->articulos_resumen->filter(function ($item) {
                                    return !isset($item->es_otro) || !$item->es_otro;
                                });
                            @endphp
                            @if($articulosFiltrados->isNotEmpty())
                                @foreach($articulosFiltrados as $articulo)
                                    <div
                                        class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <span class="text-dark">{{ $articulo->nombre }}</span>
                                        <span class="badge bg-primary rounded-pill">x{{ $articulo->cantidad }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">No hay artículos registrados.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Columna Derecha: Otros -->
                    <div class="col-md-6">
                        <h6 class="text-secondary mb-3"><i class="fa-solid fa-clipboard me-2"></i> Otros</h6>
                        <div class="bg-light rounded p-3">
                            @if($trabajo->otros->isNotEmpty())
                                @foreach($trabajo->otros as $otro)
                                    <div
                                        class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <span>
                                            {{ $otro->descripcion }}
                                            <small class="text-muted ms-1">(Otro)</small>
                                        </span>
                                        <span
                                            class="badge bg-secondary rounded-pill">x{{ (float) ($otro->cantidad ?? 1) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted mb-0">No hay otros registros.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comentarios Técnicos -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="mb-0 fw-bold text-secondary"><i class="fa-solid fa-message me-2"></i> Comentarios Técnicos
                </h5>
            </div>
            <div class="card-body p-4">
                @if($trabajo->descripcionTecnicos->isNotEmpty())
                    <div class="row g-3">
                        @foreach($trabajo->descripcionTecnicos as $comentario)
                            <div class="col-12">
                                <div class="bg-light rounded p-3 border-start border-4 border-primary">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="text-primary">
                                            <i class="fa-regular fa-user me-1"></i>
                                            {{ $comentario->user->name ?? 'Técnico' }}
                                        </strong>
                                        <small class="text-muted">
                                            <i class="fa-regular fa-calendar me-1"></i>
                                            {{ $comentario->created_at->format('d/m/Y h:i A') }}
                                        </small>
                                    </div>
                                    <p class="mb-0 text-dark">{{ $comentario->descripcion }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fa-regular fa-comment fs-1 mb-2 opacity-50"></i>
                        <p class="mb-0">No hay comentarios técnicos registrados.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Evidencias Fotográficas -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="mb-0 fw-bold text-secondary"><i class="fa-solid fa-camera me-2"></i> Evidencias Fotográficas
                </h5>
            </div>
            <div class="card-body p-4">
                @if($trabajo->evidencias->isNotEmpty())
                    <div class="row g-3">
                        @foreach($trabajo->evidencias as $evidencia)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="position-relative">
                                    <img src="{{ Storage::url($evidencia->url_foto ?? $evidencia->ruta) }}"
                                        alt="Evidencia del trabajo" loading="lazy" class="img-fluid rounded shadow-sm"
                                        style="width: 100%; height: 160px; object-fit: cover; cursor: pointer;"
                                        onclick="window.open(this.src, '_blank')">
                                    <div class="mt-1">
                                        <small class="text-muted d-block text-truncate">
                                            <i class="fa-regular fa-user me-1"></i>
                                            {{ $evidencia->user->name ?? 'Usuario' }}
                                        </small>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            <i class="fa-regular fa-calendar me-1"></i>
                                            {{ $evidencia->created_at->format('d/m/Y h:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fa-regular fa-image fs-1 mb-2 opacity-50"></i>
                        <p class="mb-0">No se adjuntaron fotos a este trabajo.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>