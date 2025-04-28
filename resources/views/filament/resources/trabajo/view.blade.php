<x-filament::page>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-filament::card>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4">
                Vehículo
            </h2>
            <div class="space-y-4">
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Placa:</span>
                    {{ $trabajo->vehiculo->placa ?? 'SIN PLACA' }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Marca:</span>
                    {{ $trabajo->vehiculo->marca?->nombre }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Modelo:</span>
                    {{ $trabajo->vehiculo->modelo?->nombre }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Color:</span>
                    {{ $trabajo->vehiculo->color }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Tipo:</span>
                    {{ $trabajo->vehiculo->tipoVehiculo->nombre }}
                </p>
                <div>
                    <p class="font-medium text-gray-800 dark:text-gray-300">Propietarios:</p>
                    <ul class="list-disc list-inside mt-2">
                        @forelse($trabajo->vehiculo->clientes as $cliente)
                            <li class="text-gray-800 dark:text-gray-300">{{ $cliente->nombre }}</li>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">No hay propietarios asignados.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4">
                Trabajo
            </h2>
            <div class="space-y-4">
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Código:</span>
                    {{ $trabajo->codigo }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Taller:</span>
                    {{ $trabajo->taller->nombre ?? 'N/A' }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Fecha Ingreso:</span>
                    {{ $trabajo->fecha_ingreso->format('d/m/y') }}
                    {{ $trabajo->hora_ingreso->isoFormat('h:mm A') }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Fecha Salida:</span>
                    {{ $trabajo->fecha_salida?->format('d/m/y') }}
                    {{ $trabajo->hora_salida?->isoFormat('h:mm A') }}
                </p>
                <div>
                    <p class="font-medium text-gray-800 dark:text-gray-300">Descripción del Servicio:</p>
                    <p class="text-gray-800 dark:text-gray-300 mt-1">{{ $trabajo->descripcion_servicio }}</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4">
                Técnicos Asignados
            </h2>
            <ul class="list-disc list-inside">
                @forelse($trabajo->usuarios as $usuario)
                    <li class="text-gray-800 dark:text-gray-300">{{ $usuario->name }}</li>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No hay técnicos asignados.</p>
                @endforelse
            </ul>
        </x-filament::card>
        <x-filament::card>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4">
                Archivos
            </h2>
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
        </x-filament::card>
    </div>

    {{--
    <h2 class="text-xl font-bold">Servicios ejecutados</h2>

    <section
        class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        <div class="fi-section-content-ctn">
            <div class="fi-section-content p-0">
                <div class="overflow-x-auto">
                    <table
                        class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5 rounded-lg">
                        <thead class="divide-y divide-gray-200 dark:divide-white/5">
                            <tr class="bg-gray-50 dark:bg-white/5">
                                <th class="fi-ta-header-cell px-6 py-3.5 fi-table-header-cell-nombre">
                                    <span
                                        class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                        <span
                                            class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Servicios
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-6 py-3.5 fi-table-header-cell-costo">
                                    <span
                                        class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                        <span
                                            class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Costo
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-6 py-3.5 fi-table-header-cell-costo">
                                    <span
                                        class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                        <span
                                            class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Cantidad
                                        </span>
                                    </span>
                                </th>
                                <th class="fi-ta-header-cell px-6 py-3.5 fi-table-header-cell-costo">
                                    <span
                                        class="group flex w-full items-center gap-x-1 whitespace-nowrap justify-start">
                                        <span
                                            class="fi-ta-header-cell-label text-sm font-semibold text-gray-950 dark:text-white">
                                            Sub-Total
                                        </span>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                            @forelse($trabajo->servicios as $trabajoServicio)
                                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5 transition duration-75">
                                    <td class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                        {{ $trabajoServicio->servicio->nombre }}
                                    </td>
                                    <td class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                        S/ {{ $trabajoServicio->precio }}
                                    </td>
                                    <td class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                        {{ $trabajoServicio->cantidad }}
                                    </td>
                                    <td class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                        S/ {{ number_format($trabajoServicio->cantidad * $trabajoServicio->precio, 2, '.', '') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center px-3 py-6 break-words">
                                        <div
                                            class="fi-ta-empty-state-content mx-auto grid max-w-lg justify-items-center text-center">
                                            <div
                                                class="fi-ta-empty-state-icon-ctn mb-4 rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                                                <svg class="fi-ta-empty-state-icon h-6 w-6 text-gray-500 dark:text-gray-400"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    aria-hidden="true" data-slot="icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18 18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                            <h4
                                                class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                                No se encontraron servicios
                                            </h4>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    --}}

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
                            src="{{ Storage::url($evidencia->evidencia_url) }}"
                            type="video/mp4">
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
                                    clip-rule="evenodd"/>
                            </svg>  
                            <span style="margin-left: 5px">{{ $evidencia->created_at->format('d/m/Y') }} ({{ $evidencia->created_at->diffForHumans() }})</span>
                        </p>
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $evidencia->observacion }}</p>
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
