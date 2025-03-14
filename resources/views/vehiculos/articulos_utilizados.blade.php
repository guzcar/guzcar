<x-layout>
    <h1 class="mb-3">Artículos Utilizados</h1>
    <a href="{{ url()->previous() }}" class="btn btn-light border mb-3">Volver</a>

    <p>Esta es la lista de todos los artículos que se utilizaron en este vehículo.</p>

    <input type="text" id="buscador" class="form-control mb-3" placeholder="Buscar artículos...">

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 table-striped table-hover">
                    <tbody id="tablaArticulos">
                        @forelse ($articulos as $articulo)
                            <tr class="articulo">
                                <td>
                                    {{ $articulo->subCategoria->categoria->nombre }}
                                    {{ $articulo->subCategoria->nombre }}
                                    {{ $articulo->especificacion }}
                                    {{ $articulo->marca }}
                                    {{ $articulo->color }} -
                                    {{ $articulo->tamano_presentacion }}
                                </td>
                            </tr>
                        @empty
                            <tr id="sinArticulos">
                                <td class="text-center text-secondary py-5">
                                    <i class="fa-regular fa-circle-xmark fs-1 mb-3"></i>
                                    <p class="mb-0">No se encontraron artículos.</p>
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
            let filas = document.querySelectorAll('.articulo');
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
            document.getElementById('sinArticulos').style.display = (!filtro && filas.length === 0) ? '' : 'none';
        });
    </script>
    @endpush
</x-layout>