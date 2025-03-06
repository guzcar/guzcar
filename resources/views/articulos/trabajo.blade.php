<x-layout>

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

    <div class="accordion" id="articulosAcordion">
        @forelse ($trabajo->trabajoArticulos as $index => $trabajoArticulo)
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading-{{ $index }}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ $index }}" aria-expanded="false" aria-controls="collapse-{{ $index }}">
                        {{ $trabajoArticulo->articulo->subCategoria->categoria->nombre }} -
                        {{ $trabajoArticulo->articulo->subCategoria->nombre }} -
                        {{ $trabajoArticulo->articulo->especificacion }}
                        {{ $trabajoArticulo->articulo->marca }}
                        {{ $trabajoArticulo->articulo->color }} -
                        {{ $trabajoArticulo->articulo->tamano_presentacion }}
                    </button>
                </h2>
                <div id="collapse-{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $index }}"
                    data-bs-parent="#articulosAcordion">
                    <div class="accordion-body">
                        <p><i class="fas text-secondary fa-fw me-2 fa-box"></i> Cantidad: {{ \App\Services\FractionService::decimalToFraction($trabajoArticulo->cantidad) }}</p>
                        <p><i class="fas text-secondary fa-fw me-2 fa-calendar-alt"></i> Día:
                            {{ $trabajoArticulo->fecha->isoFormat('dddd, D [de] MMMM') }}</p>
                        <p class="mb-0"><i class="fas text-secondary fa-fw me-2 fa-clock"></i> Hora:
                            {{ $trabajoArticulo->hora->format('h:i A') }}</p>
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