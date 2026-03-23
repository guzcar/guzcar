<x-layout>
    <div style="max-width: 1200px;" class="mx-auto pb-5">

        <h1 class="mb-4 text-dark fw-bold">Consulta Vehicular</h1>

        <form action="{{ route('consulta.buscar.vehiculo') }}" method="GET" class="mb-4">
            <label for="placa" class="mb-2 fw-semibold text-secondary">Placa del Vehículo</label>
            <div class="input-group input-group-lg shadow-sm">
                <input type="text" id="placa" name="placa" class="form-control" placeholder="Ej: ABC-123" minlength="6"
                    value="{{ old('placa', $placa ?? '') }}" required>
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
                <a href="{{ route('consulta.vehiculo.articulos', $vehiculo->id) }}"
                    class="btn btn-light border py-3 w-100 shadow-sm fw-semibold">
                    <i class="fa-solid fa-box-open me-2"></i> Ver Todos los Artículos
                </a>
                <a href="{{ route('consulta.vehiculo.servicios', $vehiculo->id) }}"
                    class="btn btn-light border py-3 w-100 shadow-sm fw-semibold">
                    <i class="fa-solid fa-wrench me-2"></i> Ver Todos los Servicios
                </a>
            </div>
            @if($trabajos && $trabajos->isNotEmpty())
                <h4 class="mb-4 mt-5 text-secondary border-bottom pb-2 fw-bold">Historial de Trabajos</h4>

                <div class="table-responsive bg-white rounded shadow-sm border">
                    <table class="table table-hover table-borderless align-middle mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="py-3 px-4" style="min-width: 180px;">Ingreso</th>
                                <th class="py-3" style="min-width: 180px;">Salida</th>
                                <th class="py-3" style="min-width: 180px;">Kilometraje</th>
                                <th class="py-3">Descripción</th>
                                <th class="py-3 px-4 text-end" style="min-width: 100px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trabajos as $trabajo)
                                @php
                                    $descripcion = trim(preg_replace('/\s+/', ' ', $trabajo->descripcion_servicio ?? 'Sin descripción registrada.'));
                                @endphp
                                <tr class="border-bottom">
                                    <td class="px-4">
                                        @if($trabajo->fecha_ingreso)
                                            <i class="fa-regular fa-calendar text-muted me-1"></i>
                                            {{ $trabajo->fecha_ingreso->format('d/m/Y') }}<br>
                                            <small class="text-muted">{{ $trabajo->fecha_ingreso->format('h:i A') }}</small>
                                        @else
                                            <i class="fa-regular fa-calendar text-muted me-1"></i>
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if($trabajo->fecha_salida)
                                            <div>
                                                <i class="fa-regular fa-calendar-check text-muted me-1"></i>
                                                {{ $trabajo->fecha_salida->format('d/m/Y') }}<br>
                                                <small class="text-muted">{{ $trabajo->fecha_salida->format('h:i A') }}</small>
                                            </div>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-2 py-1">
                                                EN TALLER
                                            </span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">
                                        {{ $trabajo->kilometraje ? number_format($trabajo->kilometraje, 0, ',', '.') . ' km' : '-' }}
                                    </td>
                                    <td style="white-space: normal; word-wrap: break-word; line-height: 1.5;">
                                        {{ $descripcion }}
                                    </td>
                                    <td class="px-4 text-end">
                                        <a href="{{ route('consulta.vehiculo.trabajo.detalle', $trabajo->id) }}"
                                            class="btn btn-primary fw-bold text-nowrap">
                                            Detalles
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    {{ $trabajos->links('pagination::bootstrap-4') }}
                </div>

            @else
            @endif

        @elseif(request()->has('placa'))
        @endif
    </div>
</x-layout>