<x-layout>

    @push('styles')
        <style>
            .articulos-table td {
                vertical-align: top;
                padding: 0.25rem 0 0.25rem 0;
            }
        </style>
    @endpush

    <h1 class="mb-3">Artículos</h1>
    <a class="btn btn-light border mb-3 py-2" href="{{ route('home') }}">Volver al Inicio</a>

    <p>Esta es la lista de artículos que solicitaste del almacén en esta semana.</p>

    @if(session('success'))
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="accordion" id="articulosAcordion">
        @forelse ($articulos as $index => $articulo)
                @php
                    // Construye el label dinámicamente en el orden especificado
                    $labelParts = [];
                    if ($articulo->articulo->categoria)
                        $labelParts[] = $articulo->articulo->categoria->nombre; // Categoría
                    if ($articulo->articulo->marca)
                        $labelParts[] = $articulo->articulo->marca->nombre; // Marca
                    if ($articulo->articulo->subCategoria)
                        $labelParts[] = $articulo->articulo->subCategoria->nombre; // Subcategoría
                    if ($articulo->articulo->especificacion)
                        $labelParts[] = $articulo->articulo->especificacion; // Especificación
                    if ($articulo->articulo->presentacion)
                        $labelParts[] = $articulo->articulo->presentacion->nombre; // Presentación
                    if ($articulo->articulo->medida)
                        $labelParts[] = $articulo->articulo->medida; // Medida
                    if ($articulo->articulo->unidad)
                        $labelParts[] = $articulo->articulo->unidad->nombre; // Unidad
                    if ($articulo->articulo->color)
                        $labelParts[] = $articulo->articulo->color; // Color
                    $label = implode(' ', $labelParts); // Une las partes con un espacio
                @endphp

                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading-{{ $index }}">
                        <table class="w-100">
                            <tr>
                                <td>
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $index }}" aria-expanded="false"
                                        aria-controls="collapse-{{ $index }}">
                                        {{ $label }}
                                    </button>
                                </td>
                                <td class="px-2" style="width: 1rem;">
                                    <form action="{{ route('gestion.trabajos.articulos.confirmar.index', $articulo) }}"
                                        method="POST">
                                        @if ($articulo->confirmado)
                                            <button class="btn btn-secondary border-0 py-2 px-3" disabled>
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            @csrf
                                            <button class="btn btn-success border-0 py-2 px-3" type="submit">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </h2>

                    <div id="collapse-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}"
                        data-bs-parent="#articulosAcordion">
                        <div class="accordion-body">
                            <table class="articulos-table">
                                <tr>
                                    <td><i class="fas text-secondary fa-fw me-2 fa-box"></i></td>
                                    <td><b>Cantidad:</b>
                                        {{ \App\Services\FractionService::decimalToFraction($articulo->cantidad) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas text-secondary fa-fw me-2 fa-calendar-alt"></i></td>
                                    <td><b>Día:</b> {{ $articulo->fecha->isoFormat('dddd, D [de] MMMM') }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas text-secondary fa-fw me-2 fa-clock"></i></td>
                                    <td><b>Hora:</b> {{ $articulo->hora->format('h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas text-secondary fa-fw me-2 fa-car"></i></td>
                                    <td>
                                        <b>Vehículo:</b>
                                        @if($articulo->trabajo)
                                            {{ $articulo->trabajo->vehiculo->placa }} -
                                            {{ $articulo->trabajo->vehiculo->tipoVehiculo->nombre }}
                                            {{ $articulo->trabajo->vehiculo->marca }}
                                            {{ $articulo->trabajo->vehiculo->modelo }}
                                            {{ $articulo->trabajo->vehiculo->color }}
                                        @else
                                            <span class="text-secondary">Taller</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid text-secondary fa-fw me-2 fa-user"></i></td>
                                    <td><b>Responsable:</b> {{ $articulo->responsable->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <p class="text-center text-secondary py-4 mb-0">No has solicitado artículos esta semana.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $articulos->links('pagination::bootstrap-4') }}
    </div>

</x-layout>