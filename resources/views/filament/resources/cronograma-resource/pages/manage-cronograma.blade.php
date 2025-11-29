<x-filament-panels::page>
    @php
        $fechas = $this->getFechasConAsignaciones();
        $tareas = $this->getTareas();
    @endphp

    <div class="space-y-4">
        {{-- Tabla de cronograma --}}
        <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg">
            <table class="w-full table-auto">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800"
                            wire:click="sortBy('fecha')">
                            <div class="flex items-center gap-2">
                                Día
                                @if($sortColumn === 'fecha')
                                    <x-heroicon-o-chevron-up class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" />
                                @endif
                            </div>
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Fecha
                        </th>
                        @foreach($tareas as $tarea)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ $tarea->nombre }}
                            </th>
                        @endforeach
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($fechas as $fechaItem)
                        @php
                            $fecha = $fechaItem->fecha;
                            $carbon = \Carbon\Carbon::parse($fecha);
                            $dia = ucfirst($carbon->locale('es')->isoFormat('dddd'));
                            $fechaFormateada = $carbon->format('d/m/Y');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $dia }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $fechaFormateada }}
                            </td>
                            @foreach($tareas as $tarea)
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    @php
                                        $userId = $this->getUsuariosParaFecha($fecha, $tarea->id);
                                    @endphp
                                    <select 
                                        wire:change="updateAsignacion('{{ $fecha }}', {{ $tarea->id }}, $event.target.value)"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm {{ !$userId ? 'text-gray-400' : '' }}"
                                        wire:key="select-{{ $fecha }}-{{ $tarea->id }}"
                                    >
                                        <option value="" {{ !$userId ? 'selected' : '' }} class="text-gray-400">
                                            
                                        </option>
                                        @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }} class="text-gray-900 dark:text-gray-100 font-normal not-italic">
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            @endforeach
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <div class="flex gap-2">
                                    <button 
                                        type="button"
                                        wire:click="duplicarDia('{{ $fecha }}')"
                                        x-data
                                        x-on:click="$dispatch('open-modal', { id: 'duplicar-{{ $fecha }}' })"
                                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                        title="Duplicar día"
                                    >
                                        <x-heroicon-o-document-duplicate class="w-5 h-5" />
                                    </button>
                                    
                                    <button 
                                        type="button"
                                        wire:click="eliminarDia('{{ $fecha }}')"
                                        wire:confirm="¿Eliminar todas las asignaciones de este día?"
                                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200"
                                        title="Eliminar día"
                                    >
                                        <x-heroicon-o-trash class="w-5 h-5" />
                                    </button>
                                </div>

                                {{-- Modal para duplicar --}}
                                <x-filament::modal id="duplicar-{{ $fecha }}" width="md">
                                    <x-slot name="heading">
                                        Duplicar asignaciones del {{ $fechaFormateada }}
                                    </x-slot>

                                    <form wire:submit="ejecutarDuplicarDia('{{ $fecha }}', $wire.nuevaFecha{{ str_replace('-', '', $fecha) }})">
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Nueva fecha
                                                </label>
                                                <input 
                                                    type="date"
                                                    wire:model="nuevaFecha{{ str_replace('-', '', $fecha) }}"
                                                    class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                                    required
                                                />
                                            </div>

                                            <div class="flex justify-end gap-3">
                                                <x-filament::button
                                                    color="gray"
                                                    x-on:click="$dispatch('close-modal', { id: 'duplicar-{{ $fecha }}' })"
                                                >
                                                    Cancelar
                                                </x-filament::button>
                                                
                                                <x-filament::button type="submit">
                                                    Duplicar
                                                </x-filament::button>
                                            </div>
                                        </div>
                                    </form>
                                </x-filament::modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($tareas) + 3 }}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                No hay asignaciones registradas. Crea una nueva asignación para comenzar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($fechas->hasPages())
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Mostrando {{ $fechas->firstItem() }} a {{ $fechas->lastItem() }} de {{ $fechas->total() }} días
                </div>
                
                {{ $fechas->links() }}
            </div>
        @endif
    </div>
</x-filament-panels::page>