<x-layout>
    <h1 class="mb-3">Vehículos Disponibles</h1>
    <a class="btn btn-light border mb-3 py-2" href="{{ route('home') }}">Volver al Inicio</a>

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
                                    <form action="{{ route('trabajos.asignar.post', $trabajo) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-success w-100 py-2" type="submit">Asignar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="sinVehiculos">
                                <td class="text-center text-secondary py-5" colspan="2">
                                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                                    <p class="mb-0">No hay vehículos disponibles.</p>
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
            document.getElementById('buscador').addEventListener('keyup', function () {
                let filtro = this.value.toLowerCase();
                // Normalizar el texto para eliminar tildes
                filtro = filtro.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                let filas = document.querySelectorAll('.vehiculo');
                let hayCoincidencias = false;

                filas.forEach(fila => {
                    let placa = fila.querySelector('li:nth-child(1)').innerText.toLowerCase();
                    let tipo = fila.querySelector('li:nth-child(2)').innerText.toLowerCase();
                    let marca = fila.querySelector('li:nth-child(3)').innerText.toLowerCase();
                    let modelo = fila.querySelector('li:nth-child(4)').innerText.toLowerCase();
                    let color = fila.querySelector('li:nth-child(5)').innerText.toLowerCase();

                    // Normalizar los textos de las filas
                    placa = placa.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    tipo = tipo.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    marca = marca.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    modelo = modelo.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                    color = color.normalize("NFD").replace(/[\u0300-\u036f]/g, "");

                    // Dividir el filtro en partes
                    let partesFiltro = filtro.split(' ');

                    // Verificar si todas las partes del filtro coinciden en alguna propiedad
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
        </script>
    @endpush
</x-layout>