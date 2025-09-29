<x-pdf-layout title="Chack List Ingreso de Vehículo" code="123" tipoReporte="CHECK LIST">
    <div class="mb-6">
        <!-- Header con información del vehículo -->
        <div class="border-b border-gray-300 pb-4 mb-4">
            <table class="w-full">
                <tr>
                    <td class="w-1/2">
                        <h1 class="text-lg font-bold">{{ $trabajo->vehiculo->placa ?? 'N/A' }}</h1>
                        <p class="text-sm text-gray-600">
                            {{ $trabajo->vehiculo->tipoVehiculo->nombre ?? '' }} 
                            {{ $trabajo->vehiculo->marca->nombre ?? '' }} 
                            {{ $trabajo->vehiculo->modelo->nombre ?? '' }}
                        </p>
                    </td>
                    <td class="w-1/2 text-right">
                        <p class="text-sm"><strong>Fecha:</strong> {{ $fecha }}</p>
                        <p class="text-sm"><strong>Checklist:</strong> Ingreso de Vehículo</p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Lista de Items de Inventario -->
        <div class="mb-6">
            <h2 class="text-md font-bold bg-gray-100 p-2 mb-3">INVENTARIO DE VEHÍCULO</h2>
            
            <!-- Items por defecto -->
            <div class="mb-4">
                <h3 class="text-sm font-semibold mb-2">Items Predefinidos:</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($itemsDefault as $item)
                        <div class="flex items-center text-sm">
                            <span class="w-4 h-4 border border-gray-400 mr-2 flex items-center justify-center">
                                @if($item['checked'])
                                    ✓
                                @endif
                            </span>
                            <span>{{ $item['nombre'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Items personalizados -->
            @if($itemsCustom->count() > 0)
            <div class="mb-4">
                <h3 class="text-sm font-semibold mb-2">Items Adicionales:</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($itemsCustom as $item)
                        <div class="flex items-center text-sm">
                            <span class="w-4 h-4 border border-gray-400 mr-2 flex items-center justify-center">
                                @if($item['checked'])
                                    ✓
                                @endif
                            </span>
                            <span>{{ $item['nombre'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Niveles y Observaciones -->
        <div class="mb-6">
            <h2 class="text-md font-bold bg-gray-100 p-2 mb-3">NIVELES Y OBSERVACIONES</h2>
            
            <div class="grid grid-cols-2 gap-6">
                <!-- Niveles de Combustible y Aceite -->
                <div>
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-2">Combustible: {{ $combustible }}%</h3>
                        <div class="flex items-center h-6 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500" style="width: {{ $combustible }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600 mt-1">
                            <span>E</span>
                            <span>¼</span>
                            <span>½</span>
                            <span>¾</span>
                            <span>F</span>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold mb-2">Aceite: {{ $aceite }}%</h3>
                        <div class="flex items-center h-6 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500" style="width: {{ $aceite }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-600 mt-1">
                            <span>E</span>
                            <span>¼</span>
                            <span>½</span>
                            <span>¾</span>
                            <span>F</span>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div>
                    <h3 class="text-sm font-semibold mb-2">Observaciones:</h3>
                    <div class="border border-gray-300 rounded p-3 min-h-32 text-sm">
                        @if($observaciones)
                            {!! nl2br(e($observaciones)) !!}
                        @else
                            <span class="text-gray-400">Sin observaciones</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Diagrama del Vehículo -->
        <div class="mb-6">
            <h2 class="text-md font-bold bg-gray-100 p-2 mb-3">DIAGRAMA DEL VEHÍCULO</h2>
            
            @if($trabajo->vehiculo?->tipoVehiculo?->diagrama)
                <div class="border border-gray-300 p-4 text-center">
                    <!-- Aquí iría la imagen del diagrama con los símbolos -->
                    <div style="position: relative; max-width: 400px; margin: 0 auto;">
                        <!-- Nota: En un entorno real, necesitarías generar una imagen del diagrama con los símbolos -->
                        <p class="text-sm text-gray-600 mb-2">Diagrama del vehículo con anotaciones</p>
                        
                        <!-- Mostrar símbolos utilizados -->
                        @if(count($symbols) > 0)
                        <div class="mt-3">
                            <h4 class="text-sm font-semibold mb-2">Símbolos utilizados:</h4>
                            <div class="flex justify-center gap-4 text-sm">
                                @foreach($symbols as $symbol)
                                    <div class="flex items-center">
                                        <span class="font-bold mx-1 
                                            @if($symbol['type'] == 'O') text-blue-600
                                            @elseif($symbol['type'] == 'X') text-red-600
                                            @elseif($symbol['type'] == '//') text-orange-600
                                            @endif">
                                            {{ $symbol['type'] }}
                                        </span>
                                        <span class="text-xs">
                                            @if($symbol['type'] == 'O') Abolladura
                                            @elseif($symbol['type'] == 'X') Quiñe
                                            @elseif($symbol['type'] == '//') Rayadura
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Total de anotaciones: {{ count($symbols) }}
                            </p>
                        </div>
                        @else
                            <p class="text-sm text-gray-500">No se realizaron anotaciones en el diagrama</p>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-4">No hay diagrama disponible para este tipo de vehículo</p>
            @endif
        </div>

        <!-- Firma -->
        <div>
            <h2 class="text-md font-bold bg-gray-100 p-2 mb-3">FIRMA DE CONFORMIDAD</h2>
            
            <div class="text-center">
                @if($firma)
                    <div class="border border-gray-300 p-4 inline-block">
                        <p class="text-sm font-semibold mb-2">Firma registrada:</p>
                        <!-- En un entorno real, aquí mostrarías la imagen de la firma -->
                        <div class="h-20 w-64 border border-gray-200 bg-gray-50 flex items-center justify-center mx-auto">
                            <span class="text-gray-500 text-sm">[Firma del cliente]</span>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500 py-4">No se registró firma</p>
                @endif
                
                <div class="mt-4 text-xs text-gray-600">
                    <p>Fecha y hora de generación: {{ $fecha }}</p>
                    <p>Checklist de Ingreso - {{ $trabajo->vehiculo->placa ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-pdf-layout>