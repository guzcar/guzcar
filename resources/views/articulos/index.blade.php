<x-layout>

    <h1 class="mb-3">Artículos</h1>
    <a class="btn btn-light border mb-3" href="{{ route('home') }}">Volver al Inicio</a>

    <p>Esta es la lista de artículos que solicitaste del inventario en esta semana.</p>

    <div class="accordion" id="articulosAcordion">
        @forelse ($articulos as $index => $articulo)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ $index }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $index }}" aria-expanded="false" aria-controls="collapse-{{ $index }}">
                        {{ $articulo->articulo->subCategoria->categoria->nombre }}
                        {{ $articulo->articulo->subCategoria->nombre }}
                        {{ $articulo->articulo->especificacion }}
                        {{ $articulo->articulo->marca }}
                        {{ $articulo->articulo->color }} -
                        {{ $articulo->articulo->tamano_presentacion }}
                    </button>
                </h2>
                <div id="collapse-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}"
                    data-bs-parent="#articulosAcordion">
                    <div class="accordion-body">
                        <p><i class="fas text-secondary me-2 fa-box"></i> Cantidad: {{ $articulo->cantidad }}</p>
                        <p><i class="fas text-secondary me-2 fa-calendar-alt"></i> Día:
                            {{ $articulo->fecha->isoFormat('dddd, D [de] MMMM') }}
                        </p>
                        <p><i class="fas text-secondary me-2 fa-clock"></i> Hora: {{ $articulo->hora->format('h:i A') }}</p>
                        <p class="mb-0"><i class="fas text-secondary me-2 fa-car"></i> Vehículo:
                            @if($articulo->trabajo)
                                {{ $articulo->trabajo->vehiculo->placa }} -
                                {{ $articulo->trabajo->vehiculo->marca }}
                                {{ $articulo->trabajo->vehiculo->modelo }}
                                {{ $articulo->trabajo->vehiculo->color }}
                            @else
                                <span class="text-secondary">Taller</span>
                            @endif
                        </p>
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