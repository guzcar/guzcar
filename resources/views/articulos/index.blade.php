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
    <a class="btn btn-light border mb-3" href="{{ route('home') }}">Volver al Inicio</a>

    <p>Esta es la lista de artículos que solicitaste del almacén en esta semana.</p>

    <div class="accordion" id="articulosAcordion">
        @forelse ($articulos as $index => $articulo)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ $index }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $index }}" aria-expanded="false" aria-controls="collapse-{{ $index }}">
                        <span class="pe-2">
                            {{ $articulo->articulo->subCategoria->categoria->nombre }}
                            {{ $articulo->articulo->subCategoria->nombre }}
                            {{ $articulo->articulo->especificacion }}
                            {{ $articulo->articulo->marca }}
                            {{ $articulo->articulo->color }} -
                            {{ $articulo->articulo->tamano_presentacion }}
                        </span>
                    </button>
                </h2>
                <div id="collapse-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}"
                    data-bs-parent="#articulosAcordion">
                    <div class="accordion-body">
                        <table class="articulos-table">
                            <tr>
                                <td><i class="fas text-secondary fa-fw me-2 fa-box"></i></td>
                                <td><b>Cantidad:</b> {{ \App\Services\FractionService::decimalToFraction($articulo->cantidad) }}</td>
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

</x-layout>