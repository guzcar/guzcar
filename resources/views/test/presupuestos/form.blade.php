@extends('test.layout')

@section('content')
    <h2>
        {{ $presupuesto->exists ? 'Editar Presupuesto #' . $presupuesto->id : 'Crear Nuevo Presupuesto' }}
    </h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>¡Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form 
        action="{{ $presupuesto->exists ? route('test.presupuestos.update', $presupuesto) : route('test.presupuestos.store') }}" 
        method="POST"
    >
        @csrf
        @if ($presupuesto->exists)
            @method('PUT')
        @endif

        {{-- Usamos la cuadrícula para los campos principales --}}
        <div class="form-grid">
            <div class="form-group">
                <label for="search_cliente">Buscar Cliente (Nombre o DNI)</label>
                <input 
                    type="text" 
                    id="search_cliente" 
                    class="form-control" 
                    list="list-clientes"
                    placeholder="Escribe para buscar..."
                    autocomplete="off"
                    value="{{ $presupuesto->cliente?->nombre }} {{ $presupuesto->cliente ? '('.$presupuesto->cliente->identificador.')' : '' }}"
                >
                <datalist id="list-clientes"></datalist>
                <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $presupuesto->cliente_id }}">
            </div>

            <div class="form-group">
                <label for="search_vehiculo">Buscar Vehículo (Placa, Marca, VIN)</label>
                <input 
                    type="text" 
                    id="search_vehiculo" 
                    class="form-control" 
                    list="list-vehiculos"
                    placeholder="Escribe para buscar..."
                    autocomplete="off"
                    value="{{ $presupuesto->vehiculo?->placa }} {{ $presupuesto->vehiculo?->marca?->nombre ? '('.$presupuesto->vehiculo->marca->nombre.')' : '' }}"
                >
                <datalist id="list-vehiculos"></datalist>
                <input type="hidden" name="vehiculo_id" id="vehiculo_id" value="{{ $presupuesto->vehiculo_id }}">
            </div>
        </div>

        <div class="form-group">
            <label for="observacion">Observación</label>
            <textarea id="observacion" name="observacion" rows="3" class="form-control">{{ old('observacion', $presupuesto->observacion) }}</textarea>
        </div>

        <hr>

        <h3>Servicios</h3>
        <div id="lista-servicios">
            @foreach (old('servicios', $presupuesto->servicios ?? []) as $index => $servicio)
                {{-- Fila de ítem con clases CSS, sin estilos en línea --}}
                <div class="item-row">
                    <input type="text" name="servicios[{{ $index }}][descripcion]" class="form-control" placeholder="Descripción" value="{{ $servicio['descripcion'] ?? $servicio->descripcion }}">
                    <input type="number" name="servicios[{{ $index }}][cantidad]" class="form-control" placeholder="Cant." value="{{ $servicio['cantidad'] ?? $servicio->cantidad }}">
                    <input type="number" step="0.01" name="servicios[{{ $index }}][precio]" class="form-control" placeholder="Precio" value="{{ $servicio['precio'] ?? $servicio->precio }}">
                    <button type="button" class="btn btn-remove-item" onclick="this.parentElement.remove()">X</button>
                </div>
            @endforeach
        </div>
        <div class="add-item-container">
            <button type="button" class="btn btn-success" onclick="addServicio()">+ Añadir Servicio</button>
        </div>
        
        <h3>Artículos / Repuestos</h3>
        <div id="lista-articulos">
            @foreach (old('articulos', $presupuesto->articulos ?? []) as $index => $articulo)
                {{-- Fila de ítem con clases CSS, sin estilos en línea --}}
                <div class="item-row">
                    <input type="text" name="articulos[{{ $index }}][descripcion]" class="form-control" placeholder="Descripción" value="{{ $articulo['descripcion'] ?? $articulo->descripcion }}">
                    <input type="number" name="articulos[{{ $index }}][cantidad]" class="form-control" placeholder="Cant." value="{{ $articulo['cantidad'] ?? $articulo->cantidad }}">
                    <input type="number" step="0.01" name="articulos[{{ $index }}][precio]" class="form-control" placeholder="Precio" value="{{ $articulo['precio'] ?? $articulo->precio }}">
                    <button type="button" class="btn btn-remove-item" onclick="this.parentElement.remove()">X</button>
                </div>
            @endforeach
        </div>
        <div class="add-item-container">
            <button type="button" class="btn btn-success" onclick="addArticulo()">+ Añadir Artículo</button>
        </div>

        {{-- Contenedor de acciones finales --}}
        <div class="form-actions">
            <a href="{{ route('test.presupuestos.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar Presupuesto</button>
        </div>
    </form>

    {{-- *** PLANTILLAS ACTUALIZADAS *** --}}

    <template id="template-servicio">
        <div class="item-row">
            <input type="text" name="servicios[__INDEX__][descripcion]" class="form-control" placeholder="Descripción">
            <input type="number" name="servicios[__INDEX__][cantidad]" class="form-control" placeholder="Cant." value="1">
            <input type="number" step="0.01" name="servicios[__INDEX__][precio]" class="form-control" placeholder="Precio" value="0.00">
            <button type="button" class="btn btn-remove-item" onclick="this.parentElement.remove()">X</button>
        </div>
    </template>
    
    <template id="template-articulo">
         <div class="item-row">
            <input type="text" name="articulos[__INDEX__][descripcion]" class="form-control" placeholder="Descripción">
            <input type="number" name="articulos[__INDEX__][cantidad]" class="form-control" placeholder="Cant." value="1">
            <input type="number" step="0.01" name="articulos[__INDEX__][precio]" class="form-control" placeholder="Precio" value="0.00">
            <button type="button" class="btn btn-remove-item" onclick="this.parentElement.remove()">X</button>
        </div>
    </template>

@endsection

@push('scripts')
    {{-- El JavaScript no necesita cambios, ya que la lógica sigue siendo la misma --}}
    <script>
        // --- LÓGICA PARA AÑADIR/QUITAR ÍTEMS ---
        function addServicio() {
            const template = document.getElementById('template-servicio').innerHTML;
            const index = new Date().getTime(); // Índice único
            const html = template.replace(/__INDEX__/g, index);
            document.getElementById('lista-servicios').insertAdjacentHTML('beforeend', html);
        }

        function addArticulo() {
            const template = document.getElementById('template-articulo').innerHTML;
            const index = new Date().getTime(); // Índice único
            const html = template.replace(/__INDEX__/g, index);
            document.getElementById('lista-articulos').insertAdjacentHTML('beforeend', html);
        }

        // --- LÓGICA DE BÚSQUEDA ASÍNCRONA (Vanilla JS) ---
        const searchCache = {
            clientes: new Map(),
            vehiculos: new Map()
        };

        async function searchDatalist(term, url, listElementId, hiddenInputId, cacheMap) {
            if (term.length < 2) return;
            if (term.length === 0) {
                document.getElementById(hiddenInputId).value = '';
            }
            const listElement = document.getElementById(listElementId);
            try {
                const response = await fetch(`${url}?term=${encodeURIComponent(term)}`);
                const data = await response.json();
                listElement.innerHTML = '';
                cacheMap.clear();
                data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.text;
                    option.dataset.id = item.id;
                    listElement.appendChild(option);
                    cacheMap.set(item.text, item.id);
                });
            } catch (error) {
                console.error('Error en la búsqueda:', error);
            }
        }

        function assignId(inputValue, hiddenInputId, cacheMap) {
            const selectedId = cacheMap.get(inputValue);
            if (selectedId) {
                document.getElementById(hiddenInputId).value = selectedId;
            }
        }

        // --- Event Listeners ---
        const searchCliente = document.getElementById('search_cliente');
        searchCliente.addEventListener('input', (e) => {
            searchDatalist(
                e.target.value, 
                "{{ route('test.search.clientes') }}", 
                'list-clientes', 
                'cliente_id', 
                searchCache.clientes
            );
        });
        searchCliente.addEventListener('change', (e) => {
            assignId(e.target.value, 'cliente_id', searchCache.clientes);
        });

        const searchVehiculo = document.getElementById('search_vehiculo');
        searchVehiculo.addEventListener('input', (e) => {
            searchDatalist(
                e.target.value, 
                "{{ route('test.search.vehiculos') }}", 
                'list-vehiculos', 
                'vehiculo_id', 
                searchCache.vehiculos
            );
        });
        searchVehiculo.addEventListener('change', (e) => {
            assignId(e.target.value, 'vehiculo_id', searchCache.vehiculos);
        });

    </script>
@endpush