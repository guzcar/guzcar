<x-layout>

    <h1 class="mb-3">Evidencias para {{ $trabajo->vehiculo->placa }}</h1>
    <a class="btn btn-light border mb-3" href="{{ route('home') }}">Volver</a>

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

    <div class="row mt-3">
        @forelse ($evidencias as $evidencia)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-img-top bg-light"
                        style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        @if ($evidencia->tipo == 'imagen')
                            <img src="{{ asset('storage/' . $evidencia->evidencia_url) }}" alt="Evidencia"
                                style="max-width: 100%; max-height: 100%; object-fit: cover;">
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
                                <td style="width: 2rem;">
                                    <i class="fas text-secondary fa-fw fa-circle-user"></i>
                                </td>
                                <td>{{ $evidencia->user->name }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas text-secondary fa-fw fa-calendar-alt"></i></td>
                                <td>{{ $evidencia->created_at->isoFormat('dddd, D [de] MMMM') }}</td>
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
    </div> <!-- Cierra el div row aquí -->

    <div class="d-flex justify-content-center mt-3">
        {{ $evidencias->links('pagination::bootstrap-4') }}
    </div>

</x-layout>