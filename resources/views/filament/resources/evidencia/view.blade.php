<x-filament::page>
    @push('styles')
        <style>
            .custom-grid {
                display: grid;
                grid-template-columns: 3fr 4fr;
                gap: 1rem;
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
                    {{ $evidencia->user->name }}
                    <span
                        style="color:#71717A; font-weight: lighter; font-size: 0.9rem;">({{ $evidencia->updated_at->diffForHumans() }})</span>
                </x-slot>
                <x-slot name="description">
                    {{ $evidencia->observacion }}
                </x-slot>
                @if ($evidencia->tipo === 'imagen')
                    <a href="{{ Storage::url($evidencia->evidencia_url) }}" data-fancybox="gallery"
                        data-caption="Actualizado el: {{ $evidencia->updated_at->format('d/m/Y H:i') }}">
                        <img src="{{ Storage::url($evidencia->evidencia_url) }}" alt="Evidencia"
                            class="w-full h-48 object-cover rounded-lg transition-transform transform hover:scale-105">
                    </a>
                @elseif ($evidencia->tipo === 'video')
                    <video controls="controls" preload="auto" class="w-full h-48 rounded-lg" name="media"
                        src="{{ Storage::url($evidencia->evidencia_url) }}" type="video/mp4">
                    </video>
                @endif
            </x-filament::section>
        </div>

        <div>
            <x-filament::section>
                <p class="font-bold text-gray-900 dark:text-white pb-4">Trabajo</p>
                <div class="space-y-0">
                    <p class="text-gray-800 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Código:</span>
                        {{ $evidencia->trabajo->codigo }}
                    </p>
                    <p class="text-gray-800 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Fecha Ingreso:</span>
                        {{ $evidencia->trabajo->fecha_ingreso }}
                    </p>
                    <p class="text-gray-800 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Fecha Salida:</span>
                        {{ $evidencia->trabajo->fecha_salida ?? 'Pendiente' }}
                    </p>
                    <p class="text-gray-800 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Taller:</span>
                        {{ $evidencia->trabajo->taller->nombre ?? 'N/A' }}
                    </p>
                </div>

                <p class="font-bold text-gray-900 dark:text-white py-4">Vehículo</p>
                <div class="space-y-0">
                    <p class="text-gray-800 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Placa:</span>
                        {{ $evidencia->trabajo->vehiculo->placa ?? 'N/A' }}
                    </p>
                    <p class="text-gray-800 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Marca:</span>
                        {{ $evidencia->trabajo->vehiculo->marca }}
                    </p>
                    <p class="text-gray-800 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Modelo:</span>
                        {{ $evidencia->trabajo->vehiculo->modelo }}
                    </p>
                    <p class="text-gray-800 dark:text-gray-300">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Color:</span>
                        {{ $evidencia->trabajo->vehiculo->color }}
                    </p>
                </div>
            </x-filament::section>
        </div>
    </div>

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