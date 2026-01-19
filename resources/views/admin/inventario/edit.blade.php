<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check List - Ingreso de Vehículo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <style>
        /* Estilos para el lienzo del diagrama y los símbolos */
        #diagram-canvas {
            position: relative;
            width: 100%;
            aspect-ratio: 1 / 1;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            overflow: hidden;
        }

        #vehicle-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            pointer-events: none;
        }

        #symbols-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .symbol {
            position: absolute;
            font-weight: bold;
            font-size: 24px;
            user-select: none;
            transform: translate(-50%, -50%);
            border-radius: 99px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .symbol.selected {
            outline: 2px dashed #3b82f6;
        }

        .symbol.O {
            color: blue;
        }

        .symbol.X {
            color: red;
        }

        .symbol.slash {
            color: orange;
        }

        /* Estilo para el modo activo */
        .mode-btn.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        /* CORRECCIÓN: Estilos corregidos para los sliders verticales */
        .level-slider-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .level-slider-container .labels {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 0.875rem;
            color: #4b5563;
            height: 150px;
        }

        /* CORRECCIÓN: Slider vertical funcional */
        .level-slider {
            -webkit-appearance: slider-vertical;
            appearance: slider-vertical;
            writing-mode: bt-lr;
            width: 20px;
            height: 150px;
            background: #e5e7eb;
            border-radius: 5px;
            outline: none;
            cursor: pointer;
            padding: 0 5px;
        }

        .level-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 30px;
            height: 15px;
            background: #3b82f6;
            cursor: pointer;
            border-radius: 3px;
            transform: rotate(90deg);
        }

        .level-slider::-moz-range-thumb {
            width: 15px;
            height: 30px;
            background: #3b82f6;
            cursor: pointer;
            border-radius: 3px;
            border: none;
        }

        .level-slider::-webkit-slider-track {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 5px;
        }

        /* MEJORA: Estilo para el select del símbolo */
        #symbol-select {
            -webkit-appearance: none;
            appearance: none;
            padding-right: 2.5rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        /* MEJORA: Ocultar porcentajes de sliders */
        .level-value {
            display: none;
        }

        /* MEJORA: Estilo para headers tipo card */
        .card-header {
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        /* CORRECCIÓN: Input de texto seleccionado por defecto */
        .checklist-item-new input[type="text"]:focus {
            border-color: #3b82f6;
            ring: 2px;
            ring-color: #3b82f6;
        }

        /* MEJORA: Estilos mejorados para items duplicados */
        .duplicate-item {
            position: relative;
            border: 1px solid #ef4444;
            background-color: #fef2f2;
            border-radius: 0.375rem;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            animation: pulse-warning 2s infinite;
        }

        .duplicate-item::before {
            /* content: "Duplicado"; */
            position: absolute;
            top: -15px;
            left: 10px;
            background: #ef4444;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 4px;
            z-index: 10;
        }

        @keyframes pulse-warning {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            70% {
                box-shadow: 0 0 0 5px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* MEJORA: Mensaje de advertencia general */
        .duplicate-warning {
            background-color: #fef3f2;
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            display: none;
        }

        .duplicate-warning.show {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slide-down 0.3s ease-out;
        }

        @keyframes slide-down {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para el buscador */
        .search-container {
            position: relative;
            margin-bottom: 1rem;
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .search-input {
            padding-left: 2.5rem;
        }

        .hidden-item {
            display: none !important;
        }

        /* MEJORA: Modal de confirmación para eliminar */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            max-width: 400px;
            width: 90%;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-content {
            transform: translateY(0);
        }

        /* MEJORA: Botón deshabilitado con mejor estilo */
        .btn-disabled {
            opacity: 0.6;
            cursor: not-allowed;
            position: relative;
        }

        .btn-disabled::after {
            content: "Hay items duplicados";
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: #374151;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
        }

        .btn-disabled:hover::after {
            opacity: 1;
        }

        /* NUEVO: Estilos para el modal de advertencia de cambios sin guardar */
        .unsaved-changes-modal {
            z-index: 1001;
            /* Mayor que el modal de eliminación */
        }

        .unsaved-changes-modal .modal-content {
            max-width: 450px;
        }

        .warning-icon {
            color: #f59e0b;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }

        .cantidad-controls {
            min-width: 80px;
        }

        .cantidad-input {
            background: white;
            font-weight: 600;
            color: #374151;
        }

        .decrement-cantidad:hover,
        .increment-cantidad:hover {
            background-color: #e5e7eb !important;
        }

        .cantidad-input:focus {
            outline: none;
            background-color: #f9fafb;
        }
    </style>
</head>

<body class="bg-gray-100 p-4 sm:p-6">
    <!-- Modal de confirmación para eliminar -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="text-lg font-bold mb-2">Confirmar eliminación</h3>
            <p class="text-gray-600 mb-4">¿Estás seguro de que quieres eliminar este item del inventario?</p>
            <div class="flex justify-end gap-2">
                <button id="cancelDelete" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Cancelar</button>
                <button id="confirmDelete"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Eliminar</button>
            </div>
        </div>
    </div>

    <!-- NUEVO: Modal de advertencia de cambios sin guardar -->
    <div id="unsavedChangesModal" class="modal-overlay unsaved-changes-modal">
        <div class="modal-content">
            <div class="flex items-start mb-4">
                <svg class="warning-icon w-6 h-8" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Cambios sin guardar</h3>
                    <p class="text-gray-600 mt-1">Tienes cambios sin guardar en el inventario. Si sales ahora, perderás
                        todos los cambios.</p>
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button id="cancelExit" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400">Permanecer</button>
                <button id="confirmExit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">Salir sin
                    guardar</button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl text-gray-800"><b>{{ $trabajo->vehiculo?->placa }}</b>
                    {{ $trabajo->vehiculo->tipoVehiculo?->nombre }} {{ $trabajo->vehiculo->marca?->nombre }}
                    {{ $trabajo->vehiculo->modelo?->nombre }}</h1>
                <h3 class="text-lg text-gray-600 mt-1">CHECK LIST - INGRESO DE VEHÍCULO</h3>
            </div>
            <a href="{{ route('filament.admin.resources.trabajos.edit', ['record' => $trabajo]) }}" id="backButton"
                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                Volver al Trabajo
            </a>
        </header>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md mb-6 shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.trabajos.inventario.update', ['trabajo' => $trabajo]) }}" method="POST"
            id="inventarioForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="flex flex-col gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="card-header">
                            <h2 class="text-xl font-bold text-gray-700">Inventario de Vehículo</h2>
                        </div>
                        @php
                            $inventarioData = $trabajo->inventario_vehiculo_ingreso ?? [];
                            $savedChecklist = collect($inventarioData['checklist'] ?? []);
                        @endphp

                        <!-- MEJORA: Advertencia general de duplicados -->
                        <div id="duplicateWarning" class="duplicate-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Hay items duplicados en la lista.</span>
                        </div>

                        <!-- Buscador de items -->
                        <div class="search-container">
                            <input type="text" id="checklist-search"
                                class="w-full border border-gray-200 rounded-md px-3 py-2 shadow-sm"
                                placeholder="Buscar items...">
                        </div>

                        <div id="checklist-container" class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            @foreach($defaultItems as $itemNombre)
                                @php
                                    $itemSaved = $savedChecklist->firstWhere('nombre', $itemNombre) ?? ['nombre' => $itemNombre, 'checked' => false, 'cantidad' => 1];
                                    $index = 'default_' . $loop->index;
                                @endphp
                                <div class="checklist-item flex items-center gap-2"
                                    data-item-name="{{ strtolower($itemNombre) }}">
                                    <input type="checkbox" name="checklist[{{ $index }}][checked]" value="1" {{ ($itemSaved['checked'] ?? false) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <input type="hidden" name="checklist[{{ $index }}][nombre]" value="{{ $itemNombre }}">
                                    <label class="ml-1 block font-medium text-gray-700 flex-1">{{ $itemNombre }}</label>

                                    <!-- Controles de cantidad -->
                                    <div class="cantidad-controls flex items-center border border-gray-300 rounded-md">
                                        <button type="button"
                                            class="decrement-cantidad w-7 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-l-md transition-colors"
                                            data-target="{{ $index }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="text" name="checklist[{{ $index }}][cantidad]"
                                            value="{{ $itemSaved['cantidad'] ?? 1 }}"
                                            class="cantidad-input w-8 h-8 text-center border-x border-gray-300 text-sm"
                                            data-target="{{ $index }}" readonly>
                                        <button type="button"
                                            class="increment-cantidad w-7 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-r-md transition-colors"
                                            data-target="{{ $index }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach

                            @foreach($savedChecklist->whereNotIn('nombre', $defaultItems) as $item)
                                @php $index = 'new_' . $loop->index; @endphp
                                <div class="checklist-item-new flex items-center col-span-1 gap-2"
                                    data-item-name="{{ strtolower($item['nombre']) }}">
                                    <!-- CORRECCIÓN: Sin checkbox para items nuevos -->
                                    <input type="hidden" name="checklist[{{ $index }}][checked]" value="1">
                                    <input type="text" name="checklist[{{ $index }}][nombre]" value="{{ $item['nombre'] }}"
                                        class="flex-1 border border-gray-200 rounded-md px-2 py-1 shadow-sm" required>

                                    <!-- Controles de cantidad para items nuevos -->
                                    <div class="cantidad-controls flex items-center border border-gray-300 rounded-md">
                                        <button type="button"
                                            class="decrement-cantidad w-7 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-l-md transition-colors"
                                            data-target="{{ $index }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="text" name="checklist[{{ $index }}][cantidad]"
                                            value="{{ $item['cantidad'] ?? 1 }}"
                                            class="cantidad-input w-8 h-8 text-center border-x border-gray-300 text-sm"
                                            data-target="{{ $index }}" readonly>
                                        <button type="button"
                                            class="increment-cantidad w-7 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-r-md transition-colors"
                                            data-target="{{ $index }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <button type="button"
                                        class="remove-checklist ml-1 text-2xl text-red-500 hover:text-red-700 transition-colors"
                                        title="Eliminar item">&times;</button>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex justify-end mt-5">
                            <button type="button" id="add-checklist"
                                class="flex items-center pr-4 pl-2 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-semibold transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Agregar Item
                            </button>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="card-header">
                            <h2 class="text-xl font-bold text-gray-700">Niveles y Observaciones</h2>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="flex justify-around">
                                <div class="text-center flex flex-col items-center">
                                    <label class="block font-medium mb-2">Combustible</label>
                                    <div class="level-slider-container">
                                        <div class="labels">
                                            <span>F</span><span>¾</span><span>½</span><span>¼</span><span>E</span></div>
                                        <input type="range" min="0" max="100"
                                            value="{{ $inventarioData['combustible'] ?? 50 }}" name="combustible"
                                            class="level-slider" orient="vertical">
                                    </div>
                                    <div class="mt-2 font-semibold level-value" id="combustible-value">
                                        {{ $inventarioData['combustible'] ?? 50 }}%</div>
                                </div>
                                <div class="text-center flex flex-col items-center">
                                    <label class="block font-medium mb-2">Aceite</label>
                                    <div class="level-slider-container">
                                        <div class="labels">
                                            <span>F</span><span>¾</span><span>½</span><span>¼</span><span>E</span></div>
                                        <input type="range" min="0" max="100"
                                            value="{{ $inventarioData['aceite'] ?? 50 }}" name="aceite"
                                            class="level-slider" orient="vertical">
                                    </div>
                                    <div class="mt-2 font-semibold level-value" id="aceite-value">
                                        {{ $inventarioData['aceite'] ?? 50 }}%</div>
                                </div>
                            </div>
                            <div class="sm:col-span-1">
                                <label for="observaciones" class="block font-medium mb-2">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" rows="8"
                                    class="w-full border border-gray-200 rounded-md px-3 py-2 shadow-sm"
                                    placeholder="Anotaciones generales...">{{ $inventarioData['observaciones'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- El resto de tu código se mantiene igual -->
                <div class="flex flex-col gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="card-header">
                            <h2 class="text-xl font-bold text-gray-700">Diagrama del Vehículo</h2>
                        </div>
                        @if($trabajo->vehiculo?->tipoVehiculo?->diagrama)
                            <div class="flex flex-wrap items-center gap-4 mb-4">
                                <div>
                                    <!-- <label class="text-sm font-medium mr-2">Símbolo:</label> -->
                                    <select id="symbol-select"
                                        class="border border-gray-300 rounded-md shadow-sm px-3 py-2">
                                        <option value="O">O - Abolladura</option>
                                        <option value="X">X - Quiñe</option>
                                        <option value="//">// - Rayadura</option>
                                    </select>
                                </div>
                                <div>
                                    <!-- <label class="text-sm font-medium mr-2">Modo:</label> -->
                                    <div class="inline-flex rounded-md shadow-sm">
                                        <button type="button" data-mode="draw"
                                            class="mode-btn active px-3 py-2 border border-gray-300 rounded-l-md">Dibujar</button>
                                        <button type="button" data-mode="edit"
                                            class="mode-btn px-3 py-2 border-t border-b border-gray-300">Editar</button>
                                        <button type="button" data-mode="delete"
                                            class="mode-btn px-3 py-2 border border-gray-300 rounded-r-md">Eliminar</button>
                                    </div>
                                </div>
                            </div>

                            <div id="diagram-canvas">
                                <img id="vehicle-image"
                                    src="{{ asset('storage/' . $trabajo->vehiculo->tipoVehiculo->diagrama) }}">
                                <div id="symbols-container"></div>
                            </div>
                        @else
                            <p class="text-gray-500">No hay diagrama disponible para este tipo de vehículo.</p>
                        @endif
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="card-header">
                            <h2 class="text-xl font-bold text-gray-700">Firma de Conformidad</h2>
                        </div>
                        <div class="w-full max-w-sm mx-auto">
                            <canvas id="signatureCanvas" class="border border-gray-300 rounded-md w-full"></canvas>
                            <div class="flex justify-between items-center mt-2">
                                <button type="button" id="clearSignature"
                                    class="text-gray-600 hover:text-gray-800 text-xs">Limpiar Firma</button>
                                <span id="signatureStatus" class="text-xs text-gray-500"></span>
                            </div>
                            <input type="hidden" name="firma" id="firmaInput"
                                value="{{ $inventarioData['firma'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-8">
                <button type="button" id="cancelButton"
                    class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                    Cancelar
                </button>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Guardar Inventario
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let symbols = {!! json_encode($inventarioData['symbols'] ?? []) !!};
            let checklistCount = {{ $savedChecklist->whereNotIn('nombre', $defaultItems)->count() }};

            let itemToDelete = null;
            let hasUnsavedChanges = false;
            let initialFormState = null;
            let isSubmitting = false;

            // Función para marcar cambios - SIMPLIFICADA Y EFECTIVA
            const markFormChanged = () => {
                if (!hasUnsavedChanges && !isSubmitting) {
                    hasUnsavedChanges = true;
                    console.log('📝 Cambios detectados en el formulario');
                }
            };

            // Función para obtener estado actual del formulario
            const getFormState = () => {
                const formData = new FormData(document.getElementById('inventarioForm'));
                const state = {};

                // Incluir todos los datos del formulario
                for (let [key, value] of formData.entries()) {
                    state[key] = value;
                }

                // Incluir símbolos del diagrama
                state.symbols = JSON.stringify(symbols);

                // Incluir estado de checkboxes visibles
                const checkboxesState = {};
                document.querySelectorAll('.checklist-item:not(.hidden-item) input[type="checkbox"]').forEach((checkbox, index) => {
                    const name = checkbox.getAttribute('name');
                    checkboxesState[name] = checkbox.checked;
                });
                state.checkboxes = JSON.stringify(checkboxesState);

                return JSON.stringify(state);
            };

            // Función para verificar si hay cambios
            const checkForUnsavedChanges = () => {
                if (!hasUnsavedChanges) return false;

                const currentState = getFormState();
                const hasChanges = initialFormState !== currentState;

                console.log('🔍 Verificando cambios:', hasChanges);
                return hasChanges;
            };

            // Inicializar estado del formulario
            const initializeFormState = () => {
                initialFormState = getFormState();
                console.log('✅ Estado inicial guardado');
            };

            // Configurar detección de cambios de forma AGRESIVA
            const setupChangeDetection = () => {
                // Detectar cualquier cambio en inputs, textareas, selects
                document.addEventListener('input', function (e) {
                    if (!e.target.closest('.hidden-item') &&
                        e.target.matches('input, textarea, select')) {
                        markFormChanged();
                    }
                });

                // Detectar cambios en checkboxes
                document.addEventListener('change', function (e) {
                    if (e.target.matches('input[type="checkbox"]') &&
                        !e.target.closest('.hidden-item')) {
                        markFormChanged();
                    }
                });

                // Detectar clicks en elementos interactivos
                document.addEventListener('click', function (e) {
                    if (e.target.matches('#add-checklist, .remove-checklist, #clearSignature, .mode-btn, .increment-cantidad, .decrement-cantidad')) {
                        markFormChanged();
                    }
                });

                // Observar cambios en el DOM
                const observer = new MutationObserver(function (mutations) {
                    let shouldMarkChange = false;
                    mutations.forEach(function (mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            shouldMarkChange = true;
                        }
                    });
                    if (shouldMarkChange) {
                        markFormChanged();
                    }
                });

                // Observar contenedores importantes
                const checklistContainer = document.getElementById('checklist-container');
                const symbolsContainer = document.getElementById('symbols-container');

                if (checklistContainer) {
                    observer.observe(checklistContainer, {
                        childList: true,
                        subtree: true
                    });
                }

                if (symbolsContainer) {
                    observer.observe(symbolsContainer, {
                        childList: true,
                        subtree: true
                    });
                }
            };

            // FUNCIONES PARA CONTROL DE CANTIDAD
            const initCantidadControls = () => {
                document.addEventListener('click', function (e) {
                    // Incrementar cantidad
                    if (e.target.closest('.increment-cantidad')) {
                        const button = e.target.closest('.increment-cantidad');
                        const targetIndex = button.dataset.target;
                        incrementCantidad(targetIndex);
                    }

                    // Decrementar cantidad
                    if (e.target.closest('.decrement-cantidad')) {
                        const button = e.target.closest('.decrement-cantidad');
                        const targetIndex = button.dataset.target;
                        decrementCantidad(targetIndex);
                    }
                });
            };

            const incrementCantidad = (targetIndex) => {
                const input = document.querySelector(`input[name="checklist[${targetIndex}][cantidad]"]`);
                if (input) {
                    let currentValue = parseInt(input.value) || 1;
                    input.value = currentValue + 1;
                    markFormChanged();
                }
            };

            const decrementCantidad = (targetIndex) => {
                const input = document.querySelector(`input[name="checklist[${targetIndex}][cantidad]"]`);
                if (input) {
                    let currentValue = parseInt(input.value) || 1;
                    if (currentValue > 1) {
                        input.value = currentValue - 1;
                        markFormChanged();
                    }
                }
            };

            // INICIALIZACIÓN DEL CHECKLIST
            const initChecklist = () => {
                // Botón agregar item
                document.getElementById('add-checklist')?.addEventListener('click', function () {
                    addChecklistItem();
                    markFormChanged(); // Marcar cambio inmediatamente
                });

                // Eliminar items
                document.getElementById('checklist-container')?.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-checklist')) {
                        showDeleteConfirmation(e.target.closest('.checklist-item-new'));
                    }
                });

                // Buscador
                initSearch();

                // Verificar duplicados inicial
                checkForDuplicates();

                // Agregar eventos a checkboxes existentes
                document.querySelectorAll('.checklist-item input[type="checkbox"]').forEach(checkbox => {
                    checkbox.addEventListener('change', markFormChanged);
                });

                // Inicializar controles de cantidad
                initCantidadControls();
            };

            // AGREGAR ITEM AL CHECKLIST
            function addChecklistItem() {
                const container = document.getElementById('checklist-container');
                const newIndex = 'new_added_' + checklistCount++;
                const newItem = document.createElement('div');
                newItem.className = 'checklist-item-new flex items-center col-span-1 gap-2';
                newItem.setAttribute('data-item-name', '');
                newItem.innerHTML = `
                <input type="hidden" name="checklist[${newIndex}][checked]" value="1">
                <input type="text" name="checklist[${newIndex}][nombre]" class="flex-1 border border-gray-200 rounded-md px-2 py-1 shadow-sm" required>
                
                <!-- Controles de cantidad para items nuevos -->
                <div class="cantidad-controls flex items-center border border-gray-300 rounded-md">
                    <button type="button" class="decrement-cantidad w-7 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-l-md transition-colors" data-target="${newIndex}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <input type="text" name="checklist[${newIndex}][cantidad]" value="1" 
                           class="cantidad-input w-8 h-8 text-center border-x border-gray-300 text-sm" 
                           data-target="${newIndex}" readonly>
                    <button type="button" class="increment-cantidad w-7 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-r-md transition-colors" data-target="${newIndex}">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
                
                <button type="button" class="remove-checklist ml-1 text-2xl text-red-500 hover:text-red-700 transition-colors" title="Eliminar item">&times;</button>
            `;
                container.appendChild(newItem);

                const textInput = newItem.querySelector('input[type="text"]');
                if (textInput) {
                    setTimeout(() => {
                        textInput.focus();
                        textInput.select();

                        // Evento para cambios en texto
                        textInput.addEventListener('input', function () {
                            newItem.setAttribute('data-item-name', this.value.toLowerCase().trim());
                            checkForDuplicates();
                            markFormChanged();
                        });
                    }, 10);
                }

                // Evento para eliminar
                newItem.querySelector('.remove-checklist').addEventListener('click', function () {
                    showDeleteConfirmation(newItem);
                });

                checkForDuplicates();
            }

            // MODAL DE ELIMINACIÓN
            const initDeleteModal = () => {
                const modal = document.getElementById('deleteModal');
                const cancelBtn = document.getElementById('cancelDelete');
                const confirmBtn = document.getElementById('confirmDelete');

                cancelBtn.addEventListener('click', () => {
                    modal.classList.remove('active');
                    itemToDelete = null;
                });

                confirmBtn.addEventListener('click', () => {
                    if (itemToDelete) {
                        itemToDelete.remove();
                        checkForDuplicates();
                        markFormChanged();
                        modal.classList.remove('active');
                        itemToDelete = null;
                    }
                });

                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                        itemToDelete = null;
                    }
                });
            };

            const showDeleteConfirmation = (itemElement) => {
                itemToDelete = itemElement;
                document.getElementById('deleteModal').classList.add('active');
            };

            // BUSCADOR
            const initSearch = () => {
                const searchInput = document.getElementById('checklist-search');
                if (!searchInput) return;

                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase().trim();
                    const items = document.querySelectorAll('.checklist-item, .checklist-item-new');

                    items.forEach(item => {
                        const itemName = item.getAttribute('data-item-name') || '';
                        if (itemName.includes(searchTerm)) {
                            item.classList.remove('hidden-item');
                        } else {
                            item.classList.add('hidden-item');
                        }
                    });
                });
            };

            // VERIFICACIÓN DE DUPLICADOS
            const checkForDuplicates = () => {
                const items = document.querySelectorAll('.checklist-item, .checklist-item-new');
                const itemNames = {};
                const duplicateWarning = document.getElementById('duplicateWarning');
                const submitBtn = document.getElementById('submitBtn');

                // Limpiar marcas anteriores
                items.forEach(item => {
                    item.classList.remove('duplicate-item');
                });

                // Encontrar duplicados
                items.forEach(item => {
                    const nameInput = item.querySelector('input[type="text"], input[type="hidden"][name*="nombre"]');
                    if (nameInput) {
                        const itemName = nameInput.value.toLowerCase().trim();

                        if (itemName) {
                            if (itemNames[itemName]) {
                                item.classList.add('duplicate-item');
                                itemNames[itemName].classList.add('duplicate-item');
                            } else {
                                itemNames[itemName] = item;
                            }
                        }
                    }
                });

                const hasDuplicates = Object.values(itemNames).some(item => item.classList.contains('duplicate-item'));

                // Actualizar UI
                if (duplicateWarning) {
                    duplicateWarning.classList.toggle('show', hasDuplicates);
                }

                if (submitBtn) {
                    submitBtn.disabled = hasDuplicates;
                    submitBtn.classList.toggle('btn-disabled', hasDuplicates);
                }
            };

            // SLIDERS
            const initSliders = () => {
                const makeSliderVertical = (slider) => {
                    if (slider) {
                        slider.setAttribute('orient', 'vertical');
                        slider.style.webkitAppearance = 'slider-vertical';
                        slider.style.appearance = 'slider-vertical';
                        slider.style.writingMode = 'bt-lr';

                        const updateSliderStyle = () => {
                            const value = slider.value;
                            const min = parseInt(slider.min) || 0;
                            const max = parseInt(slider.max) || 100;
                            const percent = ((value - min) / (max - min)) * 100;
                            slider.style.background = `linear-gradient(to top, #3b82f6 ${percent}%, #e5e7eb ${percent}%)`;
                        };

                        slider.addEventListener('input', function () {
                            updateSliderStyle();
                            markFormChanged();
                        });

                        slider.addEventListener('change', function () {
                            updateSliderStyle();
                            markFormChanged();
                        });

                        updateSliderStyle();
                    }
                };

                const combustibleSlider = document.querySelector('input[name="combustible"]');
                const aceiteSlider = document.querySelector('input[name="aceite"]');

                makeSliderVertical(combustibleSlider);
                makeSliderVertical(aceiteSlider);
            };

            // DIAGRAMA DEL VEHÍCULO
            const initDiagram = () => {
                const canvas = document.getElementById('diagram-canvas');
                if (!canvas) return;

                const symbolsContainer = document.getElementById('symbols-container');
                const symbolSelect = document.getElementById('symbol-select');
                const modeButtons = document.querySelectorAll('.mode-btn');

                let diagramMode = 'draw';
                let selectedSymbol = null;
                let isDragging = false;
                let offsetX, offsetY;

                const setMode = (newMode) => {
                    diagramMode = newMode;
                    modeButtons.forEach(btn => {
                        btn.classList.toggle('active', btn.dataset.mode === newMode);
                    });
                    canvas.style.cursor = (newMode === 'draw') ? 'crosshair' : 'default';
                    if (selectedSymbol) deselectSymbol();
                };

                const selectSymbol = (symbolElement) => {
                    deselectSymbol();
                    selectedSymbol = symbolElement;
                    selectedSymbol.classList.add('selected');
                };

                const deselectSymbol = () => {
                    if (selectedSymbol) {
                        selectedSymbol.classList.remove('selected');
                        selectedSymbol = null;
                    }
                };

                const renderSymbol = (symbol) => {
                    const el = document.createElement('div');
                    el.className = `symbol ${symbol.type === '//' ? 'slash' : symbol.type}`;
                    el.textContent = symbol.type;
                    el.style.left = symbol.x;
                    el.style.top = symbol.y;
                    el.dataset.id = symbol.id;

                    el.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (diagramMode === 'delete') {
                            symbols = symbols.filter(s => s.id != el.dataset.id);
                            el.remove();
                            markFormChanged();
                        } else if (diagramMode === 'edit') {
                            selectSymbol(el);
                        }
                    });

                    el.addEventListener('mousedown', (e) => {
                        if (diagramMode === 'edit') {
                            isDragging = true;
                            selectSymbol(e.target);
                            offsetX = e.offsetX;
                            offsetY = e.offsetY;
                            selectedSymbol.style.zIndex = 1000;
                        }
                    });

                    symbolsContainer.appendChild(el);
                };

                // Evento para agregar símbolos
                canvas.addEventListener('mousedown', (e) => {
                    if (e.target.classList.contains('symbol')) return;

                    if (diagramMode === 'draw') {
                        const rect = canvas.getBoundingClientRect();
                        const x = ((e.clientX - rect.left) / rect.width) * 100;
                        const y = ((e.clientY - rect.top) / rect.height) * 100;

                        const newSymbol = {
                            id: Date.now(),
                            type: symbolSelect.value,
                            x: `${x}%`,
                            y: `${y}%`
                        };
                        symbols.push(newSymbol);
                        renderSymbol(newSymbol);
                        markFormChanged();
                    }
                    deselectSymbol();
                });

                // Eventos para arrastrar símbolos
                document.addEventListener('mousemove', (e) => {
                    if (!isDragging || !selectedSymbol) return;

                    const rect = canvas.getBoundingClientRect();
                    let x = e.pageX - rect.left - window.scrollX;
                    let y = e.pageY - rect.top - window.scrollY;

                    let newX = x - offsetX + (selectedSymbol.offsetWidth / 2);
                    let newY = y - offsetY + (selectedSymbol.offsetHeight / 2);

                    const xPercent = Math.max(0, Math.min(100, (newX / rect.width) * 100));
                    const yPercent = Math.max(0, Math.min(100, (newY / rect.height) * 100));

                    selectedSymbol.style.left = `${xPercent}%`;
                    selectedSymbol.style.top = `${yPercent}%`;
                });

                document.addEventListener('mouseup', () => {
                    if (isDragging && selectedSymbol) {
                        const symbolData = symbols.find(s => s.id == selectedSymbol.dataset.id);
                        if (symbolData) {
                            symbolData.x = selectedSymbol.style.left;
                            symbolData.y = selectedSymbol.style.top;
                            markFormChanged();
                        }
                        selectedSymbol.style.zIndex = 10;
                    }
                    isDragging = false;
                });

                // Botones de modo
                modeButtons.forEach(btn => {
                    btn.addEventListener('click', () => setMode(btn.dataset.mode));
                });

                // Renderizar símbolos existentes
                symbols.forEach(renderSymbol);
            };

            // FIRMA
            const initSignature = () => {
                const canvas = document.getElementById('signatureCanvas');
                if (!canvas) return;

                const signaturePad = new SignaturePad(canvas, {
                    backgroundColor: 'rgb(255, 255, 255)',
                    penColor: 'rgb(0, 0, 0)',
                    minWidth: 1,
                    maxWidth: 3
                });

                const signatureInput = document.getElementById('firmaInput');
                const statusSpan = document.getElementById('signatureStatus');
                const clearButton = document.getElementById('clearSignature');

                const resizeCanvas = () => {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const rect = canvas.getBoundingClientRect();
                    canvas.width = rect.width * ratio;
                    canvas.height = (rect.width * 0.5) * ratio;
                    canvas.getContext("2d").scale(ratio, ratio);
                    signaturePad.clear();
                    if (signatureInput.value) {
                        signaturePad.fromDataURL(signatureInput.value);
                    }
                };

                const updateSignature = () => {
                    if (!signaturePad.isEmpty()) {
                        signatureInput.value = signaturePad.toDataURL();
                        statusSpan.textContent = 'Firma capturada';
                        markFormChanged(); // Cambio detectado
                    } else {
                        signatureInput.value = '';
                        statusSpan.textContent = 'Firma requerida';
                    }
                };

                // Múltiples eventos para detectar cambios en la firma
                signaturePad.addEventListener('beginStroke', markFormChanged);
                signaturePad.addEventListener('endStroke', updateSignature);

                canvas.addEventListener('mouseup', updateSignature);
                canvas.addEventListener('touchend', updateSignature);

                clearButton.addEventListener('click', () => {
                    signaturePad.clear();
                    updateSignature();
                    markFormChanged();
                });

                window.addEventListener('resize', resizeCanvas);
                resizeCanvas();

                statusSpan.textContent = signatureInput.value ? 'Firma guardada' : 'Firma requerida';
            };

            // MODAL DE CAMBIOS SIN GUARDAR
            const initUnsavedChangesModal = () => {
                const showUnsavedChangesWarning = (callback) => {
                    if (checkForUnsavedChanges()) {
                        const modal = document.getElementById('unsavedChangesModal');
                        modal.classList.add('active');

                        document.getElementById('cancelExit').onclick = () => {
                            modal.classList.remove('active');
                        };

                        document.getElementById('confirmExit').onclick = () => {
                            modal.classList.remove('active');
                            hasUnsavedChanges = false;
                            if (callback) callback();
                        };

                        return true;
                    }
                    return false;
                };

                // Botón "Volver al Trabajo"
                const backButton = document.getElementById('backButton');
                if (backButton) {
                    backButton.addEventListener('click', function (e) {
                        e.preventDefault();
                        if (!showUnsavedChangesWarning(() => {
                            window.location.href = this.href;
                        })) {
                            window.location.href = this.href;
                        }
                    });
                }

                // Botón "Cancelar"
                const cancelButton = document.getElementById('cancelButton');
                if (cancelButton) {
                    cancelButton.addEventListener('click', function () {
                        if (!showUnsavedChangesWarning(() => {
                            window.location.href = "{{ route('filament.admin.resources.trabajos.edit', ['record' => $trabajo]) }}";
                        })) {
                            window.location.href = "{{ route('filament.admin.resources.trabajos.edit', ['record' => $trabajo]) }}";
                        }
                    });
                }

                // Prevenir cierre de página
                window.addEventListener('beforeunload', function (e) {
                    if (checkForUnsavedChanges() && !isSubmitting) {
                        e.preventDefault();
                        e.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
                        return e.returnValue;
                    }
                });
            };

            // ENVÍO DEL FORMULARIO
            document.getElementById('inventarioForm')?.addEventListener('submit', function (e) {
                // Prevenir envío si hay duplicados
                if (document.querySelector('.duplicate-item')) {
                    e.preventDefault();
                    alert('No se puede guardar el inventario: hay items duplicados. Por favor, corrija los items marcados en rojo.');
                    return;
                }

                isSubmitting = true;
                hasUnsavedChanges = false;

                // Agregar símbolos al formulario
                const symbolsInput = document.createElement('input');
                symbolsInput.type = 'hidden';
                symbolsInput.name = 'symbols';
                symbolsInput.value = JSON.stringify(symbols);
                this.appendChild(symbolsInput);
            });

            // INICIALIZACIÓN COMPLETA
            const initializeApp = () => {
                console.log('🚀 Inicializando aplicación...');

                // Inicializar componentes
                initChecklist();
                initSliders();
                initDiagram();
                initSignature();
                initDeleteModal();
                initUnsavedChangesModal();

                // Inicializar estado y detección de cambios
                setTimeout(() => {
                    initializeFormState();
                    setupChangeDetection();
                    console.log('✅ Aplicación lista - Detección de cambios activada');
                }, 500);
            };

            // Iniciar la aplicación
            initializeApp();
        });
    </script>
</body>

</html>