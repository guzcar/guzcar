<!DOCTYPE html>
<html>
<head>
    <title>Inventario Vehículo - Trabajo #{{ $trabajo->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .slider-vertical {
            writing-mode: bt-lr;
            -webkit-appearance: slider-vertical;
            width: 40px;
            height: 200px;
        }
        .canvas-container {
            position: relative;
            display: inline-block;
        }
        .symbol {
            position: absolute;
            cursor: move;
            font-weight: bold;
            font-size: 20px;
            user-select: none;
        }
        .symbol.O { color: blue; }
        .symbol.X { color: red; }
        .symbol.slash { color: orange; }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Inventario Vehículo - Trabajo #{{ $trabajo->id }}</h1>
            <a href="{{ route('filament.admin.resources.trabajos.edit', ['record' => $trabajo]) }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                ← Volver al Trabajo
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.trabajos.inventario.update', ['trabajo' => $trabajo]) }}" method="POST" id="inventarioForm">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna 1: Checklist del Inventario -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-bold mb-4">Checklist de Inventario</h2>
                    
                    <div id="checklist-container">
                        @php
                            $defaultItems = [
                                'Título de Propiedad', 'SOAT', 'Permiso Lunas', 'Carnet Servicios', 
                                'Llavero', 'Seguro de Ruedad', 'Pisos', 'Plumillas', 'Llantas repuesto'
                            ];
                            
                            // Manejar datos existentes de forma segura
                            $inventarioData = $trabajo->inventario_vehiculo_ingreso ?? [];
                            $savedChecklist = $inventarioData['checklist'] ?? [];
                            
                            // Si no hay datos guardados, usar los valores por defecto
                            if (empty($savedChecklist)) {
                                $checklistItems = [];
                                foreach ($defaultItems as $item) {
                                    $checklistItems[] = [
                                        'nombre' => $item,
                                        'checked' => false
                                    ];
                                }
                            } else {
                                $checklistItems = $savedChecklist;
                            }
                        @endphp

                        @foreach($checklistItems as $index => $item)
                            <div class="checklist-item flex items-center mb-2">
                                <input type="checkbox" 
                                       name="checklist[{{ $index }}][checked]" 
                                       {{ ($item['checked'] ?? false) ? 'checked' : '' }}
                                       value="1"
                                       class="mr-2">
                                <input type="text" 
                                       name="checklist[{{ $index }}][nombre]" 
                                       value="{{ $item['nombre'] ?? '' }}"
                                       class="flex-1 border rounded px-2 py-1"
                                       required>
                                <button type="button" 
                                        class="remove-checklist ml-2 text-red-600 hover:text-red-800">
                                    ×
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" 
                            id="add-checklist" 
                            class="mt-4 inline-flex items-center px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                        + Agregar Item
                    </button>
                </div>

                <!-- Columna 2: Niveles y Observaciones -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-bold mb-4">Niveles y Observaciones</h2>
                    
                    <!-- Sliders Verticales -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center">
                            <label class="block font-medium mb-2">Nivel de Combustible</label>
                            <input type="range" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $inventarioData['combustible'] ?? 50 }}" 
                                   name="combustible"
                                   class="slider-vertical">
                            <div class="mt-2 text-sm" id="combustible-value">{{ $inventarioData['combustible'] ?? 50 }}%</div>
                        </div>
                        <div class="text-center">
                            <label class="block font-medium mb-2">Nivel de Aceite</label>
                            <input type="range" 
                                   min="0" 
                                   max="100" 
                                   value="{{ $inventarioData['aceite'] ?? 50 }}" 
                                   name="aceite"
                                   class="slider-vertical">
                            <div class="mt-2 text-sm" id="aceite-value">{{ $inventarioData['aceite'] ?? 50 }}%</div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label class="block font-medium mb-2">Observaciones</label>
                        <textarea name="observaciones" 
                                  rows="4"
                                  class="w-full border rounded px-3 py-2"
                                  placeholder="Observaciones generales...">{{ $inventarioData['observaciones'] ?? '' }}</textarea>
                    </div>
                </div>

                <!-- Columna 3: Diagrama del Vehículo -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-lg font-bold mb-4">Diagrama del Vehículo</h2>
                    
                    @if($trabajo->vehiculo && $trabajo->vehiculo->tipoVehiculo && $trabajo->vehiculo->tipoVehiculo->diagrama)
                        <div class="mb-4">
                            <label class="block font-medium mb-2">Símbolo a dibujar:</label>
                            <select id="symbol-select" class="border rounded px-3 py-2">
                                <option value="O">O - Abolladura</option>
                                <option value="X">X - Quiñe</option>
                                <option value="//">// - Rayadura</option>
                            </select>
                        </div>

                        <div class="canvas-container">
                            <img id="vehicle-image" 
                                 src="{{ asset('storage/' . $trabajo->vehiculo->tipoVehiculo->diagrama) }}" 
                                 alt="Diagrama del vehículo"
                                 class="max-w-full h-auto border"
                                 style="max-width: 300px; max-height: 300px;">
                            
                            <!-- Aquí se agregarán los símbolos dinámicamente -->
                            <div id="symbols-container"></div>
                        </div>

                        <button type="button" 
                                id="clear-symbols" 
                                class="mt-4 inline-flex items-center px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                            Limpiar Símbolos
                        </button>
                    @else
                        <p class="text-gray-500">No hay diagrama disponible para este vehículo</p>
                    @endif
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('filament.admin.resources.trabajos.edit', ['record' => $trabajo]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Cancelar
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Guardar Inventario
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variables globales
            let checklistCount = {{ count($checklistItems) }};
            let symbols = {!! json_encode($inventarioData['symbols'] ?? []) !!};
            let isDragging = false;
            let currentSymbol = null;
            let offsetX, offsetY;

            // 1. Funcionalidad del Checklist
            document.getElementById('add-checklist').addEventListener('click', function() {
                const container = document.getElementById('checklist-container');
                const newItem = document.createElement('div');
                newItem.className = 'checklist-item flex items-center mb-2';
                newItem.innerHTML = `
                    <input type="checkbox" name="checklist[${checklistCount}][checked]" value="1" class="mr-2">
                    <input type="text" name="checklist[${checklistCount}][nombre]" 
                           class="flex-1 border rounded px-2 py-1" placeholder="Nuevo item" required>
                    <button type="button" class="remove-checklist ml-2 text-red-600 hover:text-red-800">×</button>
                `;
                container.appendChild(newItem);
                checklistCount++;
            });

            // Eliminar items del checklist
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-checklist')) {
                    e.target.closest('.checklist-item').remove();
                }
            });

            // 2. Sliders verticales
            const combustibleSlider = document.querySelector('input[name="combustible"]');
            const aceiteSlider = document.querySelector('input[name="aceite"]');
            
            if (combustibleSlider) {
                combustibleSlider.addEventListener('input', function() {
                    document.getElementById('combustible-value').textContent = this.value + '%';
                });
            }
            
            if (aceiteSlider) {
                aceiteSlider.addEventListener('input', function() {
                    document.getElementById('aceite-value').textContent = this.value + '%';
                });
            }

            // 3. Funcionalidad del Diagrama del Vehículo
            const vehicleImage = document.getElementById('vehicle-image');
            const symbolsContainer = document.getElementById('symbols-container');
            const symbolSelect = document.getElementById('symbol-select');
            const clearButton = document.getElementById('clear-symbols');

            if (vehicleImage) {
                // Cargar símbolos existentes
                loadSymbols();

                // Agregar nuevo símbolo al hacer clic en la imagen
                vehicleImage.addEventListener('click', function(e) {
                    const rect = vehicleImage.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    
                    // Verificar que el clic esté dentro de la imagen
                    if (x >= 0 && x <= rect.width && y >= 0 && y <= rect.height) {
                        addSymbol(x, y, symbolSelect.value);
                    }
                });

                // Limpiar símbolos
                if (clearButton) {
                    clearButton.addEventListener('click', function() {
                        symbols = [];
                        symbolsContainer.innerHTML = '';
                    });
                }
            }

            function addSymbol(x, y, type) {
                const symbolId = Date.now();
                const symbol = {
                    id: symbolId,
                    type: type,
                    x: (x / vehicleImage.offsetWidth * 100) + '%',
                    y: (y / vehicleImage.offsetHeight * 100) + '%'
                };
                
                symbols.push(symbol);
                renderSymbol(symbol);
            }

            function renderSymbol(symbol) {
                const symbolElement = document.createElement('div');
                symbolElement.className = `symbol ${symbol.type === '//' ? 'slash' : symbol.type}`;
                symbolElement.textContent = symbol.type;
                symbolElement.style.left = symbol.x;
                symbolElement.style.top = symbol.y;
                symbolElement.dataset.id = symbol.id;

                // Hacer símbolo arrastrable
                makeDraggable(symbolElement);

                // Eliminar símbolo al hacer doble clic
                symbolElement.addEventListener('dblclick', function() {
                    symbols = symbols.filter(s => s.id != symbol.id);
                    symbolElement.remove();
                });

                symbolsContainer.appendChild(symbolElement);
            }

            function makeDraggable(element) {
                element.addEventListener('mousedown', function(e) {
                    isDragging = true;
                    currentSymbol = element;
                    offsetX = e.offsetX;
                    offsetY = e.offsetY;
                    element.style.zIndex = 1000;
                });

                document.addEventListener('mousemove', function(e) {
                    if (!isDragging || !currentSymbol) return;

                    const rect = vehicleImage.getBoundingClientRect();
                    let x = e.clientX - rect.left - offsetX;
                    let y = e.clientY - rect.top - offsetY;

                    // Mantener dentro de los límites de la imagen
                    x = Math.max(0, Math.min(x, rect.width - currentSymbol.offsetWidth));
                    y = Math.max(0, Math.min(y, rect.height - currentSymbol.offsetHeight));

                    currentSymbol.style.left = (x / rect.width * 100) + '%';
                    currentSymbol.style.top = (y / rect.height * 100) + '%';
                });

                document.addEventListener('mouseup', function() {
                    if (isDragging && currentSymbol) {
                        // Actualizar posición en el array de símbolos
                        const symbolId = parseInt(currentSymbol.dataset.id);
                        const symbolIndex = symbols.findIndex(s => s.id === symbolId);
                        if (symbolIndex !== -1) {
                            symbols[symbolIndex].x = currentSymbol.style.left;
                            symbols[symbolIndex].y = currentSymbol.style.top;
                        }
                    }
                    isDragging = false;
                    currentSymbol = null;
                });
            }

            function loadSymbols() {
                symbols.forEach(symbol => {
                    renderSymbol(symbol);
                });
            }

            // 4. Preparar datos antes de enviar el formulario
            document.getElementById('inventarioForm').addEventListener('submit', function(e) {
                // Serializar símbolos
                const symbolsInput = document.createElement('input');
                symbolsInput.type = 'hidden';
                symbolsInput.name = 'symbols';
                symbolsInput.value = JSON.stringify(symbols);
                this.appendChild(symbolsInput);
            });
        });
    </script>
</body>
</html>