<x-layout>
    <h1 class="mb-3">Vehículos Asignados</h1>
    <a class="btn btn-primary mb-3" href="{{ route('trabajos.asignar') }}">Asignarme vehículos</a>

    @if (session('success'))
        <div class="alert alert-light alert-dismissible border shadow-sm fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Input para el buscador --}}
    <input type="text" id="buscador" class="form-control mb-3" placeholder="Buscar">

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
                    <tbody id="tablaVehiculos">
                        @forelse ($trabajos as $trabajo)
                            <tr class="vehiculo">
                                <td class="px-0" style="max-width: 150px">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent"><b>{{ $trabajo->vehiculo->placa ?? 'SIN PLACA' }}</b></li>
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent">{{ $trabajo->vehiculo->marca }}</li>
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent">{{ $trabajo->vehiculo->modelo }}</li>
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent">{{ $trabajo->vehiculo->color }}</li>
                                        <li class="list-group-item px-2 py-1" style="background-color: transparent">{{ $trabajo->vehiculo->tipoVehiculo->nombre }}</li>
                                    </ul>
                                </td>
                                <td style="max-width: 100px;">
                                    <div class="d-grid gap-2">
                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Gestionar
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('evidencias.index', $trabajo) }}">
                                                        <i class="text-secondary fa-fw me-2 fa-solid fa-image"></i>
                                                        Evidencias
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('trabajos.articulos', $trabajo) }}">
                                                        <i class="text-secondary fa-fw me-2 fa-solid fa-box-archive"></i>
                                                        Artículos
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#finalizarModal{{ $trabajo->id }}">Finalizar</button>
                                        <button class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#abandonarModal{{ $trabajo->id }}">Abandonar</button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal para Finalizar -->
                            <div class="modal fade" id="finalizarModal{{ $trabajo->id }}" tabindex="-1" aria-labelledby="finalizarModalLabel{{ $trabajo->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="finalizarModalLabel{{ $trabajo->id }}">Confirmar Finalización</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Estás seguro de que deseas finalizar este trabajo? Una vez finalizado ya no podrás tener acceso, solo un administrador del sistema puede revertir esta acción.
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('trabajos.finalizar', $trabajo) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success">Finalizar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal para Abandonar -->
                            <div class="modal fade" id="abandonarModal{{ $trabajo->id }}" tabindex="-1" aria-labelledby="abandonarModalLabel{{ $trabajo->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="abandonarModalLabel{{ $trabajo->id }}">Confirmar Abandono</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            ¿Estás seguro de que deseas abandonar este trabajo?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('trabajos.abandonar', $trabajo) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Abandonar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr id="sinVehiculos">
                                <td class="text-center text-secondary py-5" colspan="2">
                                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                                    <p class="mb-0">No tienes vehículos asignados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div id="noResultados" class="text-center text-secondary py-5" style="display: none;">
                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                    <p class="mb-0">No hay coincidencias.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('buscador').addEventListener('keyup', function() {
            let filtro = this.value.toLowerCase();
            let filas = document.querySelectorAll('.vehiculo');
            let hayCoincidencias = false;

            filas.forEach(fila => {
                let texto = fila.innerText.toLowerCase();
                if(texto.includes(filtro)) {
                    fila.style.display = '';
                    hayCoincidencias = true;
                } else {
                    fila.style.display = 'none';
                }
            });

            document.getElementById('noResultados').style.display = (filtro && !hayCoincidencias) ? '' : 'none';
            document.getElementById('sinVehiculos').style.display = (!filtro && filas.length === 0) ? '' : 'none';
        });
    </script>
    @endpush
</x-layout>
