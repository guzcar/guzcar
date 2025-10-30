<x-filament::page>

    {{-- Fancybox CSS/JS (CDN) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>

    <style>
        .fi-section-content-p2 .fi-section-content {
            padding: 0.5rem !important;
        }
    </style>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Información del Vehículo --}}
        <x-filament::section class="fi-section-content-p2">
            <x-slot name="heading">Vehículo</x-slot>

            <div class="overflow-x-auto">
                <table
                    class="w-full table-auto text-left border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 w-40"
                                style="width: 120px;">Placa</th>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                @php $placa = $trabajo->vehiculo->placa ?? null; @endphp
                                @if(!empty($placa))
                                    {{ $placa }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">SIN PLACA</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Marca</th>
                            <td class="px-4 py-2">
                                @php $marca = $trabajo->vehiculo->marca->nombre ?? null; @endphp
                                @if(!empty($marca))
                                    {{ $marca }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin marca</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Modelo</th>
                            <td class="px-4 py-2">
                                @php $modelo = $trabajo->vehiculo->modelo->nombre ?? null; @endphp
                                @if(!empty($modelo))
                                    {{ $modelo }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin modelo</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Color</th>
                            <td class="px-4 py-2">
                                @php $color = $trabajo->vehiculo->color ?? null; @endphp
                                @if(!empty($color))
                                    {{ $color }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin color</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Tipo</th>
                            <td class="px-4 py-2">
                                @php $tipo = $trabajo->vehiculo->tipoVehiculo->nombre ?? null; @endphp
                                @if(!empty($tipo))
                                    {{ $tipo }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin tipo</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 align-top">Propietarios
                            </th>
                            <td class="px-4 py-2">
                                <ul class="list-disc list-inside space-y-1">
                                    @forelse($trabajo->vehiculo->clientes ?? [] as $cliente)
                                        <li>{{ $cliente->nombre }}</li>
                                    @empty
                                        <span class="text-gray-500 dark:text-gray-400">Sin propietarios</span>
                                    @endforelse
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Información del Trabajo --}}
        <x-filament::section class="fi-section-content-p2">
            <x-slot name="heading">Trabajo</x-slot>

            <div class="overflow-x-auto">
                <table
                    class="w-full table-auto text-left border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 w-40"
                                style="width: 120px;">Código</th>
                            <td class="px-4 py-2">
                                @php $codigo = $trabajo->codigo ?? null; @endphp
                                @if(!empty($codigo))
                                    {{ $codigo }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin código</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Taller</th>
                            <td class="px-4 py-2">
                                @php $taller = $trabajo->taller->nombre ?? null; @endphp
                                @if(!empty($taller))
                                    {{ $taller }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin taller</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Ingreso</th>
                            <td class="px-4 py-2">
                                @php $ingreso = $trabajo->fecha_ingreso ?? null; @endphp
                                @if(!empty($ingreso))
                                    {{ $ingreso->format('d/m/y - h:i A') }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin fecha de ingreso</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Salida</th>
                            <td class="px-4 py-2">
                                @php $salida = $trabajo->fecha_salida ?? null; @endphp
                                @if(!empty($salida))
                                    {{ $salida->format('d/m/y - h:i A') }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin fecha de salida</span>
                                @endif
                            </td>
                        </tr>

                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Kilometraje</th>
                            <td class="px-4 py-2">
                                @php $km = $trabajo->kilometraje ?? null; @endphp
                                @if(!empty($km) || $km === 0)
                                    {{ $km }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin kilometraje</span>
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 align-top">Descripción
                            </th>
                            <td class="px-4 py-2">
                                @php $desc = $trabajo->descripcion_servicio ?? null; @endphp
                                @if(!empty($desc))
                                    {{ $desc }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin descripción</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Técnicos Asignados --}}
        <x-filament::section>
            <x-slot name="heading">Técnicos Asignados</x-slot>
            <ul class="list-disc list-inside space-y-1">
                @forelse(($trabajo->usuarios ?? []) as $usuario)
                    <li class="text-gray-800 dark:text-gray-300">{{ $usuario->name }}</li>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">Sin técnicos asignados</p>
                @endforelse
            </ul>
        </x-filament::section>
    </div>

    <h2 class="text-xl font-bold">Detalles del trabajo</h2>

    @forelse($trabajo->detalles as $detalle)
        <x-filament::section>
            {!! $detalle->descripcion !!}
        </x-filament::section>
    @empty
        <x-filament::card>
            <p class="text-gray-500 dark:text-gray-400">Sin detalles</p>
        </x-filament::card>
    @endforelse

    <h2 class="text-xl font-bold">Salidas</h2>

    <x-filament::section>
        <x-slot name="heading">
            Artículos
        </x-slot>

        @php
            $items = $articulosSalidosResumen ?? collect();
        @endphp

        @if(($items instanceof \Illuminate\Support\Collection ? $items->count() : count($items ?? [])) > 0)
            <ul class="list-disc">
                @foreach(($items instanceof \Illuminate\Support\Collection) ? $items : collect($items) as $item)
                    <li class="text-gray-800 dark:text-gray-300" style="margin-left: 18px;">
                        <span class="font-medium">
                            {{ \App\Services\FractionService::decimalToFraction((float) (data_get($item, 'cantidad') ?? 0)) }}
                        </span> × {{ data_get($item, 'nombre') ?? 'Artículo' }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500 dark:text-gray-400">Sin artículos salidos.</p>
        @endif
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Otros
        </x-slot>@php
            $items = $otrosResumen ?? collect();
        @endphp

        @if(($items instanceof \Illuminate\Support\Collection ? $items->count() : count($items ?? [])) > 0)
            <ul class="list-disc">
                @foreach(($items instanceof \Illuminate\Support\Collection) ? $items : collect($items) as $item)
                    <li class="text-gray-800 dark:text-gray-300" style="margin-left: 18px;">
                        <span class="font-medium">
                            {{ \App\Services\FractionService::decimalToFraction((float) (data_get($item, 'cantidad') ?? 0)) }}
                        </span> × {{ data_get($item, 'descripcion') ?? 'Otro ítem' }}
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500 dark:text-gray-400">Sin otros ítems.</p>
        @endif
    </x-filament::section>

    <h2 class="text-xl font-bold">Descripción de los técnicos</h2>

    <x-filament::section>
        <x-slot name="heading">
            Detalles de los técnicos
        </x-slot>

        @php
            $descs = $trabajoDescripcionTecnicos ?? collect();
        @endphp

        @if(($descs instanceof \Illuminate\Support\Collection ? $descs->count() : count($descs ?? [])) > 0)
            <div class="space-y-6">
                @foreach(($descs instanceof \Illuminate\Support\Collection) ? $descs : collect($descs) as $tdt)
                    <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                        <div class="mb-2 text-sm text-gray-600 dark:text-gray-400 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                class="evidence-icon w-4 h-4 shrink-0">
                                <path fill-rule="evenodd"
                                    d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"
                                    clip-rule="evenodd" />
                            </svg>

                            <span>{{ $tdt->user->name ?? 'Usuario no disponible' }}</span>

                            @if(!empty($tdt?->created_at))
                                <span class="mx-1">•</span>
                                <time datetime="{{ optional($tdt->created_at)->toIso8601String() }}">
                                    {{ optional($tdt->created_at)->format('d/m/Y h:i A') }}
                                </time>
                            @endif
                        </div>

                        <div class="pt-2 prose max-w-none dark:prose-invert">
                            {!! $tdt->descripcion ?? '<em>Sin descripción.</em>' !!}
                        </div>
                    </div>

                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400">Sin descripciones de técnicos.</p>
        @endif
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">
            Resumen de las evidencias
        </x-slot>

        <ul>
            @forelse($observaciones as $obs)
                <li class="text-gray-800 dark:text-gray-300">{{ $obs }}</li>
            @empty
                <p class="text-gray-500 dark:text-gray-400">Sin descripciones.</p>
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
        }

        .evidence-card:hover {
            transform: scale(1.02);
        }

        /* Checkboxes ocultos por defecto */
        .evidence-checkbox {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 22px;
            height: 22px;
            z-index: 10;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            background-color: white;
            border-radius: 4px;
            padding: 8px;
            display: none;
            /* oculto por defecto */
            pointer-events: none;
            /* no clicables fuera de selección */
            box-shadow: 0 0 0 1px rgba(0, 0, 0, .15) inset;
        }

        /* En modo selección se muestran y habilitan */
        .selection-mode .evidence-checkbox {
            display: block;
            pointer-events: auto;
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
            display: block;
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

        /* Indicador de modo selección */
        .select-hint {
            display: none;
            font-size: .875rem;
            color: #6b7280;
        }

        .selection-mode .select-hint {
            display: inline-block;
        }
    </style>

    <div class="flex justify-between items-center mb-1.5">
        <div>
            <h2 class="text-xl font-bold">Evidencias</h2>
            <span class="select-hint">Modo selección activo.</span>
        </div>
        <button id="shareBtn" style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
            class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-color-custom fi-btn-color-primary fi-color-primary fi-size-md fi-btn-size-md gap-1.5 px-3 py-2 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50 fi-ac-action fi-ac-btn-action">
            <span class="fi-btn-label">Compartir</span>
        </button>
    </div>

    @if ($evidencias->isNotEmpty())
        <div class="evidence-container">
            <div class="evidence-grid">
                @foreach ($evidencias as $evidencia)
                    @php $src = Storage::url($evidencia->evidencia_url); @endphp
                    <div class="evidence-card">
                        <input type="checkbox" class="evidence-checkbox" data-src="{{ $src }}"
                            id="evidence-{{ $evidencia->id }}">

                        @if ($evidencia->tipo === 'imagen')
                            <a class="evidence-link" data-fancybox="evidencias" data-type="image" href="{{ $src }}">
                                <img src="{{ $src }}" alt="Evidencia" class="evidence-media">
                            </a>
                        @elseif ($evidencia->tipo === 'video')
                            <video controls="controls" preload="auto" class="w-full h-48 rounded-lg evidence-link" name="media"
                                src="{{ Storage::url($evidencia->evidencia_url) }}"
                                type="video/mp4">
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
                            <p class="evidence-observation">{{ $evidencia->observacion ?? 'Sin observaciones' }}</p>
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
        // Enlazado inicial de Fancybox
        function bindFancybox() {
            Fancybox.bind('[data-fancybox="evidencias"]');
        }
        bindFancybox();

        let selectionMode = false;
        const shareBtn = document.getElementById('shareBtn');
        const btnLabel = shareBtn.querySelector('.fi-btn-label');

        function updateShareLabel() {
            if (!selectionMode) {
                btnLabel.textContent = 'Compartir';
                return;
            }
            const count = document.querySelectorAll('.evidence-checkbox:checked').length;
            btnLabel.textContent = count > 0 ? `Compartir (${count})` : 'Dejar de compartir';
        }

        // Deshabilitar totalmente Fancybox en modo selección
        function disableFancybox() {
            try { Fancybox.destroy(); } catch (e) { }
            document.querySelectorAll('.evidence-link').forEach(a => {
                if (!a.dataset.hrefOriginal) a.dataset.hrefOriginal = a.getAttribute('href') || '';
                a.setAttribute('href', 'javascript:void(0)');
                // Remover atributo para que no re-binde automáticamente
                if (a.hasAttribute('data-fancybox')) {
                    a.dataset.fancyboxOriginal = a.getAttribute('data-fancybox');
                    a.removeAttribute('data-fancybox');
                }
                a.setAttribute('aria-disabled', 'true');
            });
        }

        // Restaurar Fancybox al salir de selección
        function enableFancybox() {
            document.querySelectorAll('.evidence-link').forEach(a => {
                if (a.dataset.hrefOriginal !== undefined) {
                    a.setAttribute('href', a.dataset.hrefOriginal);
                }
                if (a.dataset.fancyboxOriginal) {
                    a.setAttribute('data-fancybox', a.dataset.fancyboxOriginal);
                } else {
                    a.setAttribute('data-fancybox', 'evidencias');
                }
                a.removeAttribute('aria-disabled');
            });
            bindFancybox();
        }

        function enterSelectionMode() {
            selectionMode = true;
            document.body.classList.add('selection-mode');
            disableFancybox();
            updateShareLabel();
        }

        function exitSelectionMode(clearChecked = true) {
            selectionMode = false;
            document.body.classList.remove('selection-mode');
            if (clearChecked) {
                document.querySelectorAll('.evidence-checkbox').forEach(cb => cb.checked = false);
            }
            enableFancybox();
            updateShareLabel();
        }

        // Clicks sobre anchors: en modo selección no deben abrir nada, sólo alternar checkbox
        document.addEventListener('click', (e) => {
            const a = e.target.closest('.evidence-link');
            if (!a) return;

            if (selectionMode || a.getAttribute('aria-disabled') === 'true') {
                e.preventDefault();
                const card = a.closest('.evidence-card');
                const checkbox = card?.querySelector('.evidence-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    updateShareLabel();
                }
            }
        });

        // Click en tarjeta (fuera del anchor/checkbox) para alternar en modo selección
        document.querySelectorAll('.evidence-card').forEach(card => {
            card.addEventListener('click', function (e) {
                if (!selectionMode) return;
                if (e.target.closest('.evidence-link')) return;
                if (e.target.classList.contains('evidence-checkbox')) return;

                const checkbox = this.querySelector('.evidence-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    updateShareLabel();
                }
            });
        });

        // Cuando el usuario marca/desmarca un checkbox, actualiza el label
        document.querySelectorAll('.evidence-checkbox').forEach(cb => {
            cb.addEventListener('change', updateShareLabel);
        });

        // Botón Compartir
        shareBtn.addEventListener('click', async () => {
            if (!selectionMode) {
                // Activar modo selección (y desactivar Fancybox)
                enterSelectionMode();
                return;
            }

            // Ya estamos en modo selección
            const selected = document.querySelectorAll('.evidence-checkbox:checked');

            if (selected.length === 0) {
                // Salir de modo selección si no hay nada elegido
                exitSelectionMode(true);
                return;
            }

            // Compartir (no cerramos el modo tras compartir)
            try {
                const files = await Promise.all(
                    Array.from(selected).map(async (checkbox, idx) => {
                        const response = await fetch(checkbox.dataset.src);
                        const blob = await response.blob();
                        const ext = (blob.type?.split('/')?.[1] || 'bin').split(';')[0];
                        return new File([blob], `evidencia-${Date.now()}-${idx}.${ext}`, { type: blob.type });
                    })
                );

                if (navigator.share && navigator.canShare && navigator.canShare({ files })) {
                    await navigator.share({
                        files,
                        title: 'Evidencias del trabajo',
                        text: '{{ $trabajo->vehiculo->placa ?? '' }} {{ $trabajo->vehiculo->tipoVehiculo->nombre ?? '' }} {{ $trabajo->vehiculo->marca->nombre ?? '' }} {{ $trabajo->vehiculo->modelo->nombre ?? '' }}'
                    });
                } else {
                    const urls = Array.from(selected).map(checkbox => checkbox.dataset.src);
                    const message = `Evidencias seleccionadas:\n${urls.join('\n')}`;
                    alert(message);
                }
            } catch (error) {
                console.error('Error al compartir:', error);
            } finally {
                // Mantener en modo selección, pero limpiar checks para permitir nueva selección
                document.querySelectorAll('.evidence-checkbox').forEach(cb => cb.checked = false);
                updateShareLabel();
            }
        });
    </script>
</x-filament::page>