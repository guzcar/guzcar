<x-layout>
<div style="max-width: 900px;" class="mx-auto pb-5">

    <h1 class="mb-4 text-dark fw-bold">Consulta Vehicular</h1>

    <form action="{{ route('consulta.buscar.vehiculo') }}" method="GET" class="mb-4">
        <label for="placa" class="mb-2 fw-semibold text-secondary">Placa del Vehículo</label>
        <div class="input-group input-group-lg shadow-sm">
            <input type="text" id="placa" name="placa" class="form-control" placeholder="Ej: ABC-123" minlength="6"
                value="{{ old('placa', $placa ?? '') }}"
                required>
            <button class="btn btn-primary px-4" type="submit">
                <i class="fa-solid fa-magnifying-glass me-2"></i> Buscar
            </button>
        </div>
    </form>

    @if(isset($vehiculo))
        <div class="card shadow-sm border mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <h5 class="my-0 text-primary fw-bold">
                    <i class="fa-solid fa-car-side me-2"></i> Datos del Vehículo
                </h5>
            </div>
            <div class="card-body py-4 px-4">
                <div class="row g-4">
                    <div class="col-6 col-md-2">
                        <span class="fw-bold text-muted small d-block mb-1">Marca</span>
                        <span class="fs-6 text-dark">{{ $vehiculo->marca?->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="fw-bold text-muted small d-block mb-1">Modelo</span>
                        <span class="fs-6 text-dark">{{ $vehiculo->modelo?->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-2">
                        <span class="fw-bold text-muted small d-block mb-1">Color</span>
                        <span class="fs-6 text-dark">{{ $vehiculo->color ?? 'N/A' }}</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="fw-bold text-muted small d-block mb-1">Tipo</span>
                        <span class="fs-6 text-dark">{{ $vehiculo->tipoVehiculo?->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="col-12 col-md-2">
                        <span class="fw-bold text-muted small d-block mb-1">Año</span>
                        <span class="fs-6 text-dark">{{ $vehiculo->ano ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-3 w-100 mb-5">
            <a href="{{ route('consulta.vehiculo.articulos', $vehiculo->id) }}" class="btn btn-light border py-3 w-100 shadow-sm fw-semibold">
                <i class="fa-solid fa-box-open me-2"></i> Ver Todos los Artículos
            </a>
            <a href="{{ route('consulta.vehiculo.servicios', $vehiculo->id) }}" class="btn btn-light border py-3 w-100 shadow-sm fw-semibold">
                <i class="fa-solid fa-wrench me-2"></i> Ver Todos los Servicios
            </a>
        </div>

        @if($trabajos && $trabajos->isNotEmpty())
            <h4 class="mb-4 text-secondary border-bottom pb-2 fw-bold">Historial de Trabajos</h4>
            
            @foreach($trabajos as $trabajo)
                <div class="card mb-4 border-primary shadow-sm overflow-hidden">
                    
                    <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-6 m-0">
                            <i class="fa-regular fa-calendar-check me-2"></i> 
                            Ingreso: {{ $trabajo->fecha_ingreso?->format('d/m/Y - h:i A') ?? 'Fecha no registrada' }}
                        </span>
                        <span class="badge {{ $trabajo->fecha_salida ? 'bg-light text-success' : 'bg-warning text-dark' }} px-3 py-2">
                            {{ $trabajo->fecha_salida ? 'FINALIZADO' : 'EN TALLER' }}
                        </span>
                    </div>
                    
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded border border-light-subtle h-100">
                                    <span class="d-block text-muted small fw-bold mb-1"><i class="fa-solid fa-flag-checkered me-1"></i> Salida</span>
                                    <span class="text-dark">{{ $trabajo->fecha_salida?->format('d/m/Y h:i A') ?? 'Pendiente' }}</span>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded border border-light-subtle h-100">
                                    <span class="d-block text-muted small fw-bold mb-1"><i class="fa-solid fa-gauge-high me-1"></i> Kilometraje</span>
                                    <span class="text-dark">{{ $trabajo->kilometraje ? number_format($trabajo->kilometraje, 0, ',', '.') . ' km' : 'N/A' }}</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded border border-light-subtle h-100">
                                    <span class="d-block text-muted small fw-bold mb-1"><i class="fa-solid fa-users-gear me-1"></i> Técnicos</span>
                                    <span class="text-dark">
                                        @forelse($trabajo->usuarios as $tecnico)
                                            {{ $tecnico->name }}@if(!$loop->last), @endif
                                        @empty
                                            Sin asignar
                                        @endforelse
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 p-3 bg-light rounded border-start border-4 border-secondary">
                            <span class="text-muted fw-bold d-block mb-2">Descripción General:</span>
                            <span class="text-dark">{{ $trabajo->descripcion_servicio ?? 'Sin descripción ingresada.' }}</span>
                        </div>

                        <div class="row g-4 mt-2">
                            @if($trabajo->articulos_resumen->isNotEmpty())
                            <div class="col-md-6">
                                <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom border-primary">
                                    <i class="fa-solid fa-box me-2"></i> Artículos Despachados
                                </h6>
                                <ul class="list-group list-group-flush rounded border">
                                    @foreach($trabajo->articulos_resumen as $articulo)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                            <span class="pe-3">{{ $articulo->nombre }}</span>
                                            <span class="badge bg-primary rounded-pill px-2 py-1 flex-shrink-0">x{{ $articulo->cantidad }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if($trabajo->otros->isNotEmpty())
                            <div class="col-md-6">
                                <h6 class="fw-bold text-info mb-3 pb-2 border-bottom border-info">
                                    <i class="fa-solid fa-plus-circle me-2 text-dark"></i> Otros Repuestos
                                </h6>
                                <ul class="list-group list-group-flush rounded border">
                                    @foreach($trabajo->otros as $otro)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                            <span class="pe-3">{{ $otro->descripcion }}</span>
                                            <span class="badge bg-info text-dark rounded-pill px-2 py-1 flex-shrink-0">x{{ (float) ($otro->cantidad ?? 1) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 pt-3 border-top d-flex justify-content-end align-items-center">
                            <a href="{{ route('consulta.vehiculo.trabajo.detalles', $trabajo->id) }}" class="btn btn-secondary fw-bold">
                                <i class="fa-solid fa-file-lines me-2"></i> Trabajos 
                                <span class="badge bg-light text-secondary ms-1">{{ $trabajo->descripcion_tecnicos_count ?? 0 }}</span>
                            </a>
                            <a href="{{ route('consulta.vehiculo.trabajo.evidencias', $trabajo->id) }}" class="btn btn-primary fw-bold ms-2">
                                <i class="fa-solid fa-camera-retro me-2"></i> Evidencias 
                                <span class="badge bg-light text-primary ms-1">{{ $trabajo->evidencias_count }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center mt-5 mb-4">
                {{ $trabajos->links('pagination::bootstrap-5') }}
            </div>

        @else
            <div class="alert alert-secondary border-0 shadow-sm py-4 mt-3 text-center">
                <i class="fa-solid fa-folder-open fs-2 text-muted mb-3 d-block"></i>
                <h5 class="text-dark">Sin trabajos registrados</h5>
                <p class="text-muted mb-0">Este vehículo aún no cuenta con un historial de trabajos en el taller.</p>
            </div>
        @endif

    @elseif(request()->has('placa'))
        <div class="alert alert-danger shadow-sm py-3 px-4 border-0 mt-3 d-flex align-items-center" role="alert">
            <i class="fa-solid fa-triangle-exclamation fs-3 me-3"></i>
            <div>
                <strong>Vehículo no encontrado.</strong><br>
                <span class="small">No existen registros para la placa <b>{{ strtoupper($placa) }}</b>.</span>
            </div>
        </div>
    @endif
</div>
</x-layout>