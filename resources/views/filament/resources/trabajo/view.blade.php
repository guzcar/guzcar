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
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 w-40"
                                style="width: 120px;">Placa</th>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                {{ $trabajo->vehiculo->placa ?? 'SIN PLACA' }}
                            </td>
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
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 w-40"
                                style="width: 120px;">Código</th>
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
                                href="{{ asset('storage/' . $archivo->archivo_url) }}" target="_blank"
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

    <h2 class="text-xl font-bold">Descripción de los técnicos</h2>

    <x-filament::section>
        <x-slot name="heading">
            Detalles
        </x-slot>

        <ul class="list-disc list-inside space-y-1">
            @forelse($observaciones as $obs)
                <li class="text-gray-800 dark:text-gray-300">{{ $obs }}</li>
            @empty
                <p class="text-gray-500 dark:text-gray-400">Sin observaciones.</p>
            @endforelse
        </ul>
    </x-filament::section>

    <style>
        .evidence-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .share-btn-container {
            margin-bottom: 1rem;
        }

        .share-btn {
            padding: 0.5rem 1rem;
            background-color: #3b82f6;
            color: white;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s;
        }

        .share-btn:hover {
            background-color: #2563eb;
        }

        .evidence-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
        }

        @media (min-width: 640px) {
            .evidence-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 768px) {
            .evidence-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .evidence-card {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            cursor: pointer;
        }

        .evidence-card:hover {
            transform: scale(1.02);
        }

        .evidence-checkbox {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 20px;
            height: 20px;
            z-index: 10;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-color: white;
            border-radius: 4px;
            padding: 8px;
        }

        .evidence-checkbox:checked {
            background-color: #1e66daff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 16px;
        }

        .evidence-media {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 0.5rem 0.5rem 0 0;
        }

        .evidence-info {
            padding: 1rem;
            background-color: white;
        }

        .dark .evidence-info {
            background-color: #1f2937;
        }

        .evidence-user,
        .evidence-date {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .dark .evidence-user,
        .dark .evidence-date {
            color: #9ca3af;
        }

        .evidence-icon {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.5rem;
            color: #6b7280;
        }

        .dark .evidence-icon {
            color: #9ca3af;
        }

        .evidence-observation {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #374151;
        }

        .dark .evidence-observation {
            color: #e5e7eb;
        }
    </style>

    <div class="flex justify-between items-center mb-1.5">
        <h2 class="text-xl font-bold">Evidencias</h2>
        <button id="shareBtn" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
            class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
            <span class="fi-btn-label">
                Compartir
            </span>
        </button>
    </div>

    @if ($evidencias->isNotEmpty())
        <div class="evidence-container">

            <div class="evidence-grid">
                @foreach ($evidencias as $evidencia)
                    <div class="evidence-card">
                        <input type="checkbox" class="evidence-checkbox"
                            data-src="{{ Storage::url($evidencia->evidencia_url) }}" id="evidence-{{ $evidencia->id }}">

                        @if ($evidencia->tipo === 'imagen')
                            <img src="{{ Storage::url($evidencia->evidencia_url) }}" alt="Evidencia" class="evidence-media">
                        @elseif ($evidencia->tipo === 'video')
                            <video controls class="evidence-media" preload="metadata">
                                <source src="{{ Storage::url($evidencia->evidencia_url) }}" type="video/mp4">
                            </video>
                        @endif

                        <div class="evidence-info">
                            <p class="evidence-user">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="evidence-icon">
                                    <path fill-rule="evenodd"
                                        d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>{{ $evidencia->user->name }}</span>
                            </p>
                            <p class="evidence-date">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="evidence-icon">
                                    <path fill-rule="evenodd"
                                        d="M6 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3V6a3 3 0 0 0-3-3H6Zm12 2H6a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1ZM8 8V7h2v1H8Zm6 0V7h2v1h-2ZM7 11h10v2H7v-2Zm0 4h10v2H7v-2Z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>{{ $evidencia->created_at->format('d/m/Y') }}
                                    ({{ $evidencia->created_at->diffForHumans() }})</span>
                            </p>
                            <p class="evidence-observation">
                                {{ $evidencia->observacion ?? 'Sin observaciones' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
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

    <script>
        // Manejar el clic en las tarjetas
        document.querySelectorAll('.evidence-card').forEach(card => {
            card.addEventListener('click', function (e) {
                // Si el clic fue directamente en el checkbox, no hacer nada adicional
                if (e.target.classList.contains('evidence-checkbox')) {
                    return;
                }

                // Buscar el checkbox dentro de esta tarjeta
                const checkbox = this.querySelector('.evidence-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                }
            });
        });

        // Manejar el botón de compartir
        document.getElementById('shareBtn').addEventListener('click', async () => {
            const selected = document.querySelectorAll('.evidence-checkbox:checked');

            if (selected.length === 0) {
                alert("¡Selecciona al menos una evidencia!");
                return;
            }

            try {
                const files = await Promise.all(
                    Array.from(selected).map(async (checkbox) => {
                        const response = await fetch(checkbox.dataset.src);
                        const blob = await response.blob();
                        return new File([blob], `evidencia-${Date.now()}.${blob.type.split('/')[1]}`, { type: blob.type });
                    })
                );

                if (navigator.share && navigator.canShare({ files })) {
                    await navigator.share({
                        files,
                        title: 'Evidencias del trabajo',
                        text: '{{ $trabajo->vehiculo->placa }} {{ $trabajo->vehiculo->tipoVehiculo->nombre }} {{ $trabajo->vehiculo->marca->nombre }} {{ $trabajo->vehiculo->modelo->nombre }}'
                    });
                } else {
                    // Alternativa para navegadores que no soportan compartir archivos
                    const urls = Array.from(selected).map(checkbox => checkbox.dataset.src);
                    const message = `Evidencias seleccionadas:\n${urls.join('\n')}`;
                    alert(message);
                }
            } catch (error) {
                console.error('Error al compartir:', error);
                alert("Ocurrió un error al intentar compartir las evidencias.");
            }
        });
    </script>
</x-filament::page>