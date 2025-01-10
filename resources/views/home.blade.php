<x-layout>
    <h1 class="mb-3">Vehículos Asignados</h1>
    <a class="btn btn-primary mb-3" href="{{ route('trabajos.asignar') }}">Asignarme vehículos</a>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="py-3">Vehículo</th>
                            <th class="py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trabajos as $trabajo)
                            <tr>
                                <td class="px-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent"><b>{{ $trabajo->vehiculo->placa ?? 'N/A' }}</b></li>
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent">{{ $trabajo->vehiculo->marca }}</li>
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent">{{ $trabajo->vehiculo->modelo }}</li>
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent">{{ $trabajo->vehiculo->color }}</li>
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent">{{ $trabajo->vehiculo->tipoVehiculo->nombre }}</li>
                                    </ul>
                                </td>
                                <td style="max-width: 100px;">
                                    <div class="d-grid gap-2">
                                        <a class="btn btn-primary" href="{{ route('evidencias.index', $trabajo) }}">Evidencias</a>
                                        <form action="{{ route('trabajos.abandonar', $trabajo) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger w-100" type="submit">Abandonar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-secondary py-5" colspan="2">
                                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                                    <p class="mb-0">No tienes vehículos asignados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>