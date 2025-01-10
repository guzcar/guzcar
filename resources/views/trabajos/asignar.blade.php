<x-layout>
    <h1 class="mb-3">Vehículos Disponibles</h1>
    <a class="btn btn-secondary mb-3" href="{{ route('home') }}">Volver</a>
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
                                    <form action="{{ route('trabajos.asignar.post', $trabajo) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-success w-100" type="submit">Asignar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center text-secondary py-5" colspan="2">
                                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                                    <p class="mb-0">No hay vehículos disponibles.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layout>