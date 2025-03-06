<x-filament::page>
    @push('styles')
        <style>
            .custom-grid {
                display: grid;
                grid-template-columns: 4fr 3fr;
                gap: 1rem;
            }

            .custom-link {
                color: #2563EB;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
            }

            .custom-link:hover {
                text-decoration: underline;
            }

            @media (max-width: 768px) {
                .custom-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endpush

    <div class="custom-grid">
        <div>
            <x-filament::section>
                <x-slot name="heading">
                    {{ $entrada->guia }}
                </x-slot>
                <p><b>Fecha: </b>{{ \Carbon\Carbon::parse($entrada->fecha)->format('d/m/Y') }}</p>
                <p><b>Hora: </b>{{ $entrada->hora }}</p>
                <p><b>Responsable:</b> {{ $entrada->responsable->name }}</p>
                @if ($entrada->observacion)
                    <p><b>Observación:</b></p>
                    <p>{{ $entrada->observacion }}</p>
                @endif
            </x-filament::section>
        </div>

        @if ($entrada->evidencia_url)
            <div>
                <x-filament::section>
                    <x-slot name="heading">
                        Evidencia
                    </x-slot>
                    <a href="{{ asset('storage/' . $entrada->evidencia_url) }}" target="_blank" class="custom-link">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 16px; height: 16px; margin-right: 5px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                        <span>Ver Evidencia</span>
                    </a>
                </x-filament::section>
            </div>
        @endif
    </div>

    <h2 class="text-xl font-bold">Artículos</h2>

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
                                            Artículo
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
                                            SubTotal
                                        </span>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                            
                            @php
                                $total = 0;
                            @endphp
                        
                            @forelse($entrada->entradaArticulos as $entradaArticulo)

                                @php
                                    $subtotal = $entradaArticulo->cantidad * $entradaArticulo->costo;
                                    $total += $subtotal;
                                @endphp

                                <tr class="fi-ta-row hover:bg-gray-50 dark:hover:bg-white/5 transition duration-75">
                                    <td class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                        {{ $entradaArticulo->articulo->subCategoria->categoria->nombre }}
                                        {{ $entradaArticulo->articulo->subcategoria->nombre }}
                                        {{ $entradaArticulo->articulo->especificacion }}
                                        {{ $entradaArticulo->articulo->marca }}
                                        {{ $entradaArticulo->articulo->tamano_presentacion }}
                                    </td>
                                    <td class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                        S/ {{ $entradaArticulo->costo }}
                                    </td>
                                    <td class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                        {{ $entradaArticulo->cantidad }}
                                    </td>
                                    <td class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs">
                                        S/ {{ number_format($subtotal, 2, '.', '') }}
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
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true"
                                                    data-slot="icon">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18 18 6M6 6l12 12"></path>
                                                </svg>
                                            </div>
                                            <h4
                                                class="fi-ta-empty-state-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                                                No se encontraron articuloss
                                            </h4>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="fi-ta-row bg-gray-100 dark:bg-gray-800">
                                <td colspan="3" style="text-align: left;"
                                    class="fi-ta-cell px-6 py-4 text-right font-semibold text-gray-950 dark:text-white">
                                    Total
                                </td>
                                <td
                                    style="text-align: right;"
                                    class="fi-ta-cell px-6 py-4 text-gray-700 dark:text-gray-300 truncate max-w-xs font-semibold">
                                    S/ {{ number_format($total, 2, '.', '') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

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