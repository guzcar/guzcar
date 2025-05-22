<x-layout>
<div style="max-width: 750px;" class="mx-auto">

    <h1>{{ $vehiculo->placa  }}</h1>
    <h4 class="fw-normal mb-3">
        {{ $vehiculo->tipoVehiculo?->nombre }}
        {{ $vehiculo->marca?->nombre }}
        {{ $vehiculo->modelo?->nombre }}
        {{ $vehiculo?->color }}
    </h4>

    <a href="{{ url()->previous() }}" class="btn btn-light border mb-3">Volver</a>

    <p>Esta es la lista de todos los servicios que se ejecutaron en este vehículo.</p>

    <input type="text" id="buscador" class="form-control mb-3" placeholder="Buscar servicios...">

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 table-striped table-hover">
                    <tbody id="tablaServicios">
                        @forelse ($servicios as $servicio)
                            <tr class="servicio">
                                <td>{{ $servicio->nombre }}</td>
                            </tr>
                        @empty
                            <tr id="sinServicios">
                                <td class="text-center text-secondary py-5">
                                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                                    <p class="mb-0">No se encontraron servicios.</p>
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
            let filas = document.querySelectorAll('.servicio');
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
            document.getElementById('sinServicios').style.display = (!filtro && filas.length === 0) ? '' : 'none';
        });
    </script>
    @endpush

</div>
</x-layout>