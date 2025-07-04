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

        @can('update_trabajo')
        <x-filament::section>
            <x-slot name="heading">
                Archivos
            </x-slot>
            <ul class="list-disc list-inside">
                @forelse ($trabajo->archivos as $archivo)
                <li>
                    <a class="font-medium text-primary-600 dark:text-primary-500 max-w-full truncate"
                       style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap; text-decoration: none;"
                       onmouseover="this.style.textDecoration='underline';"
                       onmouseout="this.style.textDecoration='none';"
                       href="{{ asset('storage/' . $archivo->archivo_url) }}"
                       target="_blank"
                       title="{{ basename($archivo->archivo_url) }}">
                        {{ basename($archivo->archivo_url) }}
                    </a>
                </li>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No hay archivos.</p>
                @endforelse
            </ul>
        </x-filament::section>
        @endcan
    </div>

    <h2 class="text-xl font-bold">Detalles del trabajo</h2>

    @forelse($trabajo->detalles as $detalle)
        <x-filament::section>
            {!! $detalle->descripcion !!}
        </x-filament::section>
    @empty
        <x-filament::card>
            <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                    <svg class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    No se encontraron detalles
                </h4>
            </div>
        </x-filament::card>

    @endforelse

    <h2 class="text-xl font-bold">Evidencias</h2>

    @if ($evidencias->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($evidencias as $evidencia)
                <x-filament::card>
                    @if ($evidencia->tipo === 'imagen')
                        <a href="{{ Storage::url($evidencia->evidencia_url) }}" data-fancybox="gallery"
                            data-caption="Cargado el {{ $evidencia->created_at->isoFormat('D [de] MMMM [de] YYYY [a las] h:mm A') }}">
                            <img src="{{ Storage::url($evidencia->evidencia_url) }}" alt="Evidencia"
                                class="w-full h-48 object-cover rounded-lg transition-transform transform hover:scale-105">
                        </a>
                    @elseif ($evidencia->tipo === 'video')
                        <video controls="controls" preload="auto" class="w-full h-48 rounded-lg" name="media"
                            src="{{ Storage::url($evidencia->evidencia_url) }}" type="video/mp4">
                        </video>
                    @endif
                    <div class="pt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5 text-gray-500">
                                <path fill-rule="evenodd"
                                    d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span style="margin-left: 5px">{{ $evidencia->user->name }}</span>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="w-5 h-5 text-gray-500">
                                <path fill-rule="evenodd"
                                    d="M6 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H6Zm12 2H6a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1ZM8 8V7h2v1H8Zm6 0V7h2v1h-2ZM7 11h10v2H7v-2Zm0 4h10v2H7v-2Z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span style="margin-left: 5px">{{ $evidencia->created_at->format('d/m/Y') }}
                                ({{ $evidencia->created_at->diffForHumans() }})</span>
                        </p>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $evidencia->observacion ?? 'Sin observaciones' }}</p>
                    </div>
                </x-filament::card>
            @endforeach
        </div>
    @else
        <x-filament::card>
            <div class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                <div class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                    <svg class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h4 class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    No se encontraron evidencias
                </h4>
            </div>
        </x-filament::card>
    @endif

    {{ $evidencias->links() }}

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
        <script>
            Fancybox.bind("[data-fancybox]", {
                infinite: true
            });
        </script>
    @endpush
</x-filament::page>