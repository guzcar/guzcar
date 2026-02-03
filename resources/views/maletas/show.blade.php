<x-layout>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Contenido de Maleta: <span class="text-primary">{{ $maleta->codigo }}</span></h1>
        <a href="{{ route('maletas.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <h5 class="card-title">Información General</h5>
            <p class="mb-0"><strong>Observación:</strong> {{ $maleta->observacion ?? 'Ninguna' }}</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="fa-solid fa-list-ul me-2"></i>Lista de Herramientas</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="ps-4">Herramienta</th>
                            {{--<th scope="col" class="text-center">Estado</th>--}}
                            <th scope="col" class="text-center">Evidencia (Foto)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detalles as $detalle)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $detalle->herramienta->nombre ?? 'Herramienta no encontrada' }}</div>
                                    {{-- <small class="text-muted">ID: {{ $detalle->herramienta_id }}</small> --}}
                                </td>
                                {{-- <td class="text-center">
                                    @php
                                        $estadoColor = match($detalle->ultimo_estado) {
                                            'BUENO', 'NUEVO' => 'success',
                                            'REGULAR' => 'warning',
                                            'MALO', 'DAÑADO' => 'danger',
                                            default => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $estadoColor }}">
                                        {{ $detalle->ultimo_estado ?? 'SIN ESTADO' }}
                                    </span>
                                </td> --}}
                                <td class="text-center">
                                    @if($detalle->evidencia_url)
                                        {{-- 
                                            Lógica solicitada:
                                            1. Miniatura visible (img) con un tamaño fijo.
                                            2. Enlace (a) que envuelve la imagen.
                                            3. target="_blank" para abrir en pestaña nueva.
                                        --}}
                                        <a href="{{ \Illuminate\Support\Facades\Storage::url($detalle->evidencia_url) }}" target="_blank">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($detalle->evidencia_url) }}" 
                                                alt="Evidencia" 
                                                class="img-thumbnail border-primary"
                                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;">
                                        </a>
                                    @else
                                        <span class="text-muted small">
                                            <i class="fa-solid fa-image-slash"></i> Sin evidencia
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    Esta maleta no tiene herramientas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>