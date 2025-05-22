<x-layout>
<div style="max-width: 750px;" class="mx-auto">

    <h1 class="mb-3">Consulta Vehicular</h1>

    <form action="{{ route('consulta.buscar.vehiculo') }}" method="GET">
        <label for="placa" class="mb-3">Placa del Vehículo</label>
        <div class="input-group mb-3">
            <input type="text" id="placa" name="placa" class="form-control" placeholder="ABC-123" minlength="7"
                value="{{ old('placa', $placa ?? '') }}"
                required>
            <button class="btn btn-light border py-2 px-3" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
                <span class="ms-2">Buscar</span>
            </button>
        </div>
    </form>

    @if(isset($vehiculo))
        <ul class="list-group mb-3">
            <li class="list-group-item">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Marca</span>
                    <span>{{ $vehiculo->marca?->nombre }}</span>
                </div>
            </li>
            <li class="list-group-item">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Modelo</span>
                    <span>{{ $vehiculo->modelo?->nombre }}</span>
                </div>
            </li>
            <li class="list-group-item">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Color</span>
                    <span>{{ $vehiculo->color }}</span>
                </div>
            </li>
            <li class="list-group-item">
                <div class="d-flex">
                    <span class="fw-bold" style="min-width: 100px;">Tipo</span>
                    <span>{{ $vehiculo->tipoVehiculo->nombre }}</span>
                </div>
            </li>
        </ul>
        <div class="d-flex gap-3 w-100 mb-3">
            <a href="{{ route('consulta.vehiculo.articulos', $vehiculo->id) }}" class="btn btn-primary p-2 w-50">Artículos</a>
            <a href="{{ route('consulta.vehiculo.servicios', $vehiculo->id) }}" class="btn btn-primary p-2 w-50">Servicios</a>
        </div>

        @if($trabajos->isNotEmpty())
        
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-striped table-hover">
                            <tbody>
                                @foreach($trabajos as $trabajo)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="d-flex mb-2">
                                                <span class="fw-bold" style="min-width: 100px;">F. Ingreso</span>
                                                <span>{{ $trabajo?->fecha_ingreso?->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="d-flex mb-2">
                                                <span class="fw-bold" style="min-width: 100px;">F. Salida</span>
                                                @if($trabajo?->fecha_salida)
                                                    <span>{{ $trabajo->fecha_salida->format('d/m/Y') }}</span>
                                                @else
                                                    <span class="text-muted">EN TALLER</span>
                                                @endif
                                            </div>
                                            <div class="d-flex mb-2">
                                                <span class="fw-bold" style="min-width: 100px;">Descripción</span>
                                                <span>{{ $trabajo->descripcion_servicio ?? 'Sin descripción' }}</span>
                                            </div>
                                            <div class="d-flex">
                                                <span class="fw-bold" style="min-width: 100px;">Técnicos</span>
                                                <span>
                                                    @forelse($trabajo->usuarios as $tecnico)
                                                        {{ $tecnico->name }}{{ !$loop->last ? ',' : '' }}
                                                    @empty
                                                        <span class="text-muted">Sin técnicos</span>
                                                    @endforelse
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-light border">
                Este vehículo no tiene trabajos registrados.
            </div>
        @endif
    @elseif(request()->has('placa'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <p class="my-0">No se encontró ningún vehículo con la placa <b>{{ $placa }}</b>. Asegúrate de que esté
                correctamente escrita.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
</x-layout>