<x-layout>

    <h1 class="mb-3">Consulta Vehicular</h1>

    <form action="{{ route('consulta.buscar.vehiculo') }}" method="GET">
        <label for="placa" class="mb-3">Placa del Vehículo</label>
        <div class="input-group mb-3">
            <input type="text" id="placa" name="placa" class="form-control" placeholder="ABC-123" minlength="7"
                value="{{ old('placa', $placa ?? '') }}"
                required>
            <button class="btn btn-light border py-3 px-4" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span class="ms-2">Buscar</span>
            </button>
        </div>
    </form>

    @if(isset($vehiculo))
        <ul class="list-group mb-3">
            <li class="list-group-item py-3">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Marca</span>
                    <span>{{ $vehiculo->marca }}</span>
                </div>
            </li>
            <li class="list-group-item py-3">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Modelo</span>
                    <span>{{ $vehiculo->modelo }}</span>
                </div>
            </li>
            <li class="list-group-item py-3">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Color</span>
                    <span>{{ $vehiculo->color }}</span>
                </div>
            </li>
            <li class="list-group-item py-3">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Tipo</span>
                    <span>{{ $vehiculo->tipoVehiculo->nombre }}</span>
                </div>
            </li>
        </ul>
        <div class="d-flex gap-3 w-100">
            <a href="{{ route('consulta.vehiculo.articulos', $vehiculo->id) }}" class="btn btn-primary p-3 w-50">Artículos</a>
            <a href="{{ route('consulta.vehiculo.servicios', $vehiculo->id) }}" class="btn btn-primary p-3 w-50">Servicios</a>
        </div>
    @elseif(request()->has('placa'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <p class="my-0">No se encontró ningún vehículo con la placa <b>{{ $placa }}</b>. Asegúrate de que esté
                correctamente escrita.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</x-layout>