<x-layout>

    @push('styles')
        <style>
            .articulos-table td {
                vertical-align: top;
                padding: 0.25rem 0 0.25rem 0;
            }
        </style>
    @endpush

    <h1 class="mb-3">Artículos para {{ $trabajo->vehiculo->placa }}</h1>

    <div class="d-flex justify-content-between mb-3">
        <a class="btn btn-light border" href="{{ route('home') }}">Volver</a>
    </div>

    <ul class="list-group mb-3">
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Marca</span>
                <span>{{ $trabajo->vehiculo->marca }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Modelo</span>
                <span>{{ $trabajo->vehiculo->modelo }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Color</span>
                <span>{{ $trabajo->vehiculo->color }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Tipo</span>
                <span>{{ $trabajo->vehiculo->tipoVehiculo->nombre }}</span>
            </div>
        </li>
    </ul>

    <p>Esta es la lista de todos los artículos que solicitaste para este vehícuo.</p>

    @if(session('success'))
        <div class="alert alert-primary alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="accordion" id="articulosAcordion">
        @forelse ($trabajo->trabajoArticulos as $index => $trabajoArticulo)
                @php
                    $articulo = $trabajoArticulo->articulo;
                    $labelParts = [];
                    if ($articulo->categoria)
                        $labelParts[] = $articulo->categoria->nombre;
                    if ($articulo->marca)
                        $labelParts[] = $articulo->marca->nombre;
                    if ($articulo->subCategoria)
                        $labelParts[] = $articulo->subCategoria->nombre;
                    if ($articulo->especificacion)
                        $labelParts[] = $articulo->especificacion;
                    if ($articulo->presentacion)
                        $labelParts[] = $articulo->presentacion->nombre;
                    if ($articulo->medida)
                        $labelParts[] = $articulo->medida;
                    if ($articulo->unidad)
                        $labelParts[] = $articulo->unidad->nombre;
                    if ($articulo->color)
                        $labelParts[] = $articulo->color;
                    $label = implode(' ', $labelParts);
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
                                    <form action="{{ route('gestion.trabajos.articulos.confirmar.trabajo', $trabajoArticulo) }}"
                                        method="POST">
                                        @if ($trabajoArticulo->confirmado)
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
                                        {{ \App\Services\FractionService::decimalToFraction($trabajoArticulo->cantidad) }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas text-secondary fa-fw me-2 fa-calendar-alt"></i></td>
                                    <td><b>Día:</b> {{ $trabajoArticulo->fecha->isoFormat('dddd, D [de] MMMM') }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fas text-secondary fa-fw me-2 fa-clock"></i></td>
                                    <td><b>Hora:</b> {{ $trabajoArticulo->hora->format('h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td><i class="fa-solid text-secondary fa-fw me-2 fa-user"></i></td>
                                    <td><b>Responsable:</b> {{ $trabajoArticulo->responsable->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <p class="text-center text-secondary py-4 mb-0">No has solicitado artículos para este vehículo.</p>
                </div>
            </div>
        @endforelse
    </div>
</x-layout>