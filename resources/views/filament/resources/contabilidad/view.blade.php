<x-filament::page>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Información del Vehículo --}}
        <x-filament::section>
            <x-slot name="heading">
                Vehículo
            </x-slot>

            <div class="overflow-x-auto">
                <table
                    class="w-full table-auto text-left border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 w-40" style="width: 120px;">Placa</th>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                {{ $trabajo->vehiculo->placa ?? 'SIN PLACA' }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Marca</th>
                            <td class="px-4 py-2">{{ $trabajo->vehiculo->marca?->nombre }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Modelo</th>
                            <td class="px-4 py-2">{{ $trabajo->vehiculo->modelo?->nombre }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Color</th>
                            <td class="px-4 py-2">{{ $trabajo->vehiculo->color }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Tipo</th>
                            <td class="px-4 py-2">{{ $trabajo->vehiculo->tipoVehiculo->nombre }}</td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 align-top">Propietarios
                            </th>
                            <td class="px-4 py-2">
                                <ul class="list-disc list-inside space-y-1">
                                    @forelse($trabajo->vehiculo->clientes as $cliente)
                                        <li>{{ $cliente->nombre }}</li>
                                    @empty
                                        <span class="text-gray-500 dark:text-gray-400">No hay propietarios asignados.</span>
                                    @endforelse
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Información del Trabajo --}}
        <x-filament::section>
            <x-slot name="heading">
                Trabajo
            </x-slot>

            <div class="overflow-x-auto">
                <table
                    class="w-full table-auto text-left border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 w-40" style="width: 120px;">Código</th>
                            <td class="px-4 py-2">{{ $trabajo->codigo }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Taller</th>
                            <td class="px-4 py-2">{{ $trabajo->taller->nombre ?? 'N/A' }}</td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Ingreso</th>
                            <td class="px-4 py-2">
                                {{ $trabajo->fecha_ingreso->format('d/m/y - h:m A') }}
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Salida</th>
                            <td class="px-4 py-2">
                                @if($trabajo->fecha_salida)
                                    {{ $trabajo->fecha_salida->format('d/m/y - h:i A') }}
                                @else
                                    <span class="text-gray-400">Sin fecha de salida</span>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Kilometraje</th>
                            <td class="px-4 py-2">
                                @if(!empty($trabajo->kilometraje))
                                    {{ $trabajo->kilometraje }}
                                @else
                                    <span class="text-gray-400">Sin kilometraje</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 align-top">Descripción
                            </th>
                            <td class="px-4 py-2">{{ $trabajo->descripcion_servicio }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Técnicos Asignados --}}
        <x-filament::section>
            <x-slot name="heading">
                Técnicos Asignados
            </x-slot>
            <ul class="list-disc list-inside space-y-1">
                @forelse($trabajo->usuarios as $usuario)
                    <li class="text-gray-800 dark:text-gray-300">{{ $usuario->name }}</li>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No hay técnicos asignados.</p>
                @endforelse
            </ul>
        </x-filament::section>
    </div>
    

    <h2 class="text-xl font-bold">Descripción de los técnicos</h2>

    <x-filament::section>
        <x-slot name="heading">
            Detalles
        </x-slot>

        <ul class="list-disc list-inside space-y-1">
            @forelse($observaciones as $obs)
                <li class="text-gray-800 dark:text-gray-300">{{ $obs }}</li>
            @empty
                <p class="text-gray-500 dark:text-gray-400">Sin descripciones.</p>
            @endforelse
        </ul>
    </x-filament::section>
</x-filament::page>