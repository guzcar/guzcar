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
                            @php
                                $labelParts = [];
                                if ($articulo->categoria)
                                    $labelParts[] = $articulo->categoria->nombre;
                                if ($articulo->marca)
                                    $labelParts[] = $articulo->marca->nombre;
                                if ($articulo->subCategoria)
                                    $labelParts[] = $articulo->subCategoria->nombre;
                                if ($articulo->especificacion)
                                    $labelParts[] = $articulo->especificacion;
                                if ($articulo->presentacion)
                                    $labelParts[] = $articulo->presentacion->nombre;
                                if ($articulo->medida)
                                    $labelParts[] = $articulo->medida;
                                if ($articulo->unidad)
                                    $labelParts[] = $articulo->unidad->nombre;
                                if ($articulo->color)
                                    $labelParts[] = $articulo->color;
                                $label = implode(' ', $labelParts);
                            @endphp
                            <tr class="articulo" data-label="{{ strtolower($label) }}">
                                <td>
                                    {{ $label }}
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
            let filtro = this.value.trim().toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            let filas = document.querySelectorAll('.articulo');
            let hayCoincidencias = false;
            
            // Si el filtro está vacío, mostramos todos los artículos
            if (!filtro) {
                filas.forEach(fila => {
                    fila.style.display = '';
                });
                document.getElementById('noResultados').style.display = 'none';
                document.getElementById('sinArticulos').style.display = filas.length === 0 ? '' : 'none';
                return;
            }
            
            // Dividimos el filtro en palabras individuales
            let palabras = filtro.split(/\s+/);
            
            filas.forEach(fila => {
                let texto = fila.dataset.label.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                let coincide = true;
                
                // Verificamos que todas las palabras del filtro estén presentes en el texto
                for (let palabra of palabras) {
                    if (palabra && !texto.includes(palabra)) {
                        coincide = false;
                        break;
                    }
                }
                
                if (coincide) {
                    fila.style.display = '';
                    hayCoincidencias = true;
                } else {
                    fila.style.display = 'none';
                }
            });

            document.getElementById('noResultados').style.display = hayCoincidencias ? 'none' : '';
            document.getElementById('sinArticulos').style.display = 'none';
        });
    </script>
    @endpush
</x-layout>
