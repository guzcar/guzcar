<x-layout>
    <h1 class="mb-3">Vehículos Asignados</h1>
    <a class="btn btn-primary mb-3 py-2" href="{{ route('trabajos.asignar') }}">Asignarme vehículos</a>

    @if (session('success'))
        <div class="alert alert-primary alert-dismissible shadow-sm fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Input para el buscador --}}
    <input type="text" id="buscador" class="form-control mb-3 py-2" placeholder="Buscar">

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
                                        <li class="list-group-item px-2 py-2" style="background-color: transparent">
                                            <b>{{ $trabajo->vehiculo->placa ?? 'SIN PLACA' }}</b></li>
                                        <li class="list-group-item px-2 py-2" style="background-color: transparent">
                                            {{ $trabajo->vehiculo->tipoVehiculo->nombre }}</li>
                                        <li class="list-group-item px-2 py-2" style="background-color: transparent">
                                            {{ $trabajo->vehiculo->marca?->nombre }}</li>
                                        <li class="list-group-item px-2 py-2" style="background-color: transparent">
                                            {{ $trabajo->vehiculo->modelo?->nombre }}</li>
                                        <li class="list-group-item px-2 py-2" style="background-color: transparent">
                                            {{ $trabajo->vehiculo->color }}</li>
                                    </ul>
                                </td>
                                <td style="max-width: 100px;">
                                    <div class="d-grid gap-2">
                                        <div class="dropdown">
                                            <button class="btn btn-primary dropdown-toggle w-100 py-2" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                Gestionar
                                            </button>
                                            <ul class="dropdown-menu py-2">
                                                <li>
                                                    <a class="dropdown-item py-2"
                                                        href="{{ route('gestion.evidencias.index', $trabajo) }}">
                                                        <i class="text-secondary fa-fw me-2 fa-regular fa-image"></i>
                                                        Mis evidencias
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2"
                                                        href="{{ route('gestion.evidencias.all', $trabajo) }}">
                                                        <i class="text-secondary fa-fw me-2 fa-solid fa-images"></i>
                                                        Todas las evidencias
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2"
                                                        href="{{ route('gestion.trabajos.articulos', $trabajo) }}">
                                                        <i class="text-secondary fa-fw me-2 fa-solid fa-box-archive"></i>
                                                        Artículos
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <button class="btn btn-success w-100 py-2 btn-finalizar" 
                                            data-id="{{ $trabajo->id }}" 
                                            data-url="{{ route('trabajos.finalizar', $trabajo) }}">
                                            Finalizar
                                        </button>
                                        <button class="btn btn-danger w-100 py-2 btn-abandonar" 
                                            data-id="{{ $trabajo->id }}" 
                                            data-url="{{ route('trabajos.abandonar', $trabajo) }}">
                                            Abandonar
                                        </button>
                                    </div>
                                </td>
                            </tr>
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

    {{-- Modal para Finalizar --}}
    <div class="modal fade" id="finalizarModal" tabindex="-1" aria-labelledby="finalizarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="finalizarModalLabel">Confirmar Finalización</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas finalizar este trabajo? Una vez finalizado ya no podrás tener acceso, solo un administrador del sistema puede revertir esta acción.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <form id="finalizarForm" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success">Finalizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal para Abandonar --}}
    <div class="modal fade" id="abandonarModal" tabindex="-1" aria-labelledby="abandonarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="abandonarModalLabel">Confirmar Abandono</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas abandonar este trabajo?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancelar</button>
                    <form id="abandonarForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Abandonar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Manejar el modal de Finalizar
                document.querySelectorAll('.btn-finalizar').forEach(button => {
                    button.addEventListener('click', function () {
                        const url = this.getAttribute('data-url');
                        document.getElementById('finalizarForm').action = url;

                        const modal = new bootstrap.Modal(document.getElementById('finalizarModal'));
                        modal.show();
                    });
                });

                // Manejar el modal de Abandonar
                document.querySelectorAll('.btn-abandonar').forEach(button => {
                    button.addEventListener('click', function () {
                        const url = this.getAttribute('data-url');
                        document.getElementById('abandonarForm').action = url;

                        const modal = new bootstrap.Modal(document.getElementById('abandonarModal'));
                        modal.show();
                    });
                });

                // Lógica del buscador (sin cambios)
                document.getElementById('buscador').addEventListener('keyup', function () {
                    let filtro = this.value.toLowerCase();
                    filtro = filtro.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    let filas = document.querySelectorAll('.vehiculo');
                    let hayCoincidencias = false;

                    filas.forEach(fila => {
                        let placa = fila.querySelector('li:nth-child(1)').innerText.toLowerCase();
                        let tipo = fila.querySelector('li:nth-child(2)').innerText.toLowerCase();
                        let marca = fila.querySelector('li:nth-child(3)').innerText.toLowerCase();
                        let modelo = fila.querySelector('li:nth-child(4)').innerText.toLowerCase();
                        let color = fila.querySelector('li:nth-child(5)').innerText.toLowerCase();

                        placa = placa.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                        tipo = tipo.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                        marca = marca.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                        modelo = modelo.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                        color = color.normalize("NFD").replace(/[\u0300-\u036f]/g, "");

                        let partesFiltro = filtro.split(' ');

                        let coincide = partesFiltro.every(parte =>
                            placa.includes(parte) ||
                            tipo.includes(parte) ||
                            marca.includes(parte) ||
                            modelo.includes(parte) ||
                            color.includes(parte)
                        );

                        if (coincide) {
                            fila.style.display = '';
                            hayCoincidencias = true;
                        } else {
                            fila.style.display = 'none';
                        }
                    });

                    document.getElementById('noResultados').style.display = (filtro && !hayCoincidencias) ? '' : 'none';
                    document.getElementById('sinVehiculos').style.display = (!filtro && filas.length === 0) ? '' : 'none';
                });
            });
        </script>
    @endpush
</x-layout>