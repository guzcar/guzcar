<x-layout>

    <h1 class="mb-3">Descripciones para {{ $trabajo->vehiculo->placa ?? '-' }}</h1>
    <a class="btn btn-light border mb-3 py-2" href="{{ route('home') }}">Volver</a>

    <ul class="list-group mb-3">
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Tipo</span>
                <span>{{ $trabajo->vehiculo->tipoVehiculo->nombre }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Marca</span>
                <span>{{ $trabajo->vehiculo->marca->nombre ?? '-' }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Modelo</span>
                <span>{{ $trabajo->vehiculo->modelo->nombre ?? '-' }}</span>
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
                <span class="fw-bold" style="min-width: 100px;">Ingreso</span>
                <span>{{ $trabajo->fecha_ingreso->isoFormat('dddd, D [de] MMMM') }}</span>
            </div>
        </li>
        @if ($trabajo->fecha_salida)
            <li class="list-group-item">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Salida</span>
                    <span class="text-danger">
                        {{ $trabajo->fecha_salida->isoFormat('dddd, D [de] MMMM') }}
                    </span>
                </div>
            </li>
        @endif
    </ul>

    <div class="row mt-3">
        @forelse ($detalles as $detalle)
            <div class="col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <table class="mb-2">
                            <tr>
                                <td style="width: 2rem;"><i class="fas text-secondary fa-fw fa-circle-user"></i></td>
                                <td class="fw-medium">{{ $detalle->user->name }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas text-secondary fa-fw fa-calendar-alt"></i></td>
                                <td class="text-secondary">{{ $detalle->created_at->isoFormat('dddd, D [de] MMMM') }}</td>
                            </tr>
                        </table>

                        <hr class="my-3">

                        {{-- IMPORTANTE: `descripcion` contiene HTML, se renderiza con {!! !!} --}}
                        <div class="descripcion-html">
                            {!! $detalle->descripcion !!}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-center text-secondary py-4 mb-0">
                            Aún no hay descripciones para este trabajo en el vehículo.
                        </p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $detalles->links('pagination::bootstrap-4') }}
    </div>

    @push('styles')
        <style>
            /* Ajustes para contenido HTML incrustado */
            .descripcion-html img { max-width: 100%; height: auto; }
            .descripcion-html table { width: 100%; }
            .descripcion-html blockquote { border-left: 4px solid #e9ecef; padding-left: .75rem; color: #6c757d; }
            .descripcion-html pre { background: #f8f9fa; padding: .75rem; border-radius: .25rem; overflow: auto; }
        </style>
    @endpush

</x-layout>
