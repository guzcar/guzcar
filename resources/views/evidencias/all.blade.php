<x-layout>

    <h1 class="mb-3">Evidencias para {{ $trabajo->vehiculo->placa }}</h1>
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
                <span>{{ $trabajo->vehiculo->marca?->nombre }}</span>
            </div>
        </li>
        <li class="list-group-item">
            <div class="d-flex">
                <span class="fw-bold" style="min-width: 100px;">Modelo</span>
                <span>{{ $trabajo->vehiculo->modelo?->nombre }}</span>
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
        @forelse ($evidencias as $evidencia)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-img-top bg-light"
                        style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        @if ($evidencia->tipo == 'imagen')
                            <a href="{{ Storage::url($evidencia->evidencia_url) }}" data-fancybox="gallery"
                                data-caption="{{ $evidencia->user->name }} ({{ $evidencia->created_at->isoFormat('dddd, D [de] MMMM') }})">
                                <img src="{{ Storage::url($evidencia->evidencia_url) }}" alt="Evidencia"
                                    style="max-width: 100%; max-height: 100%; object-fit: cover;">
                            </a>
                        @elseif ($evidencia->tipo == 'video')
                            <video controls style="max-width: 100%; max-height: 100%; object-fit: cover;">
                                <source src="{{ asset('storage/' . $evidencia->evidencia_url) }}" type="video/mp4">
                                Tu navegador no soporta la etiqueta de video.
                            </video>
                        @endif
                    </div>
                    <div class="card-body">
                        <table>
                            <tr>
                                <td style="width: 2rem;"><i class="fas text-secondary fa-fw fa-circle-user"></i></td>
                                <td class="fw-medium">{{ $evidencia->user->name }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas text-secondary fa-fw fa-calendar-alt"></i></td>
                                <td class="text-secondary">{{ $evidencia->created_at->isoFormat('dddd, D [de] MMMM') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-center text-secondary py-4 mb-0">Aún no hay evidencias para este vehículo.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $evidencias->links('pagination::bootstrap-4') }}
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
        <script>
            Fancybox.bind("[data-fancybox]", {
                infinite: true
            });
        </script>
    @endpush

</x-layout>