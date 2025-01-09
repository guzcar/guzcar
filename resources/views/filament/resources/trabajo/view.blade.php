<x-filament::page>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-filament::card>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4">
                Vehículo
            </h2>
            <div class="space-y-4">
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Placa:</span>
                    {{ $trabajo->vehiculo->placa ?? 'N/A' }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Marca:</span>
                    {{ $trabajo->vehiculo->marca }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Modelo:</span>
                    {{ $trabajo->vehiculo->modelo }}
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
                    <span class="font-medium text-gray-900 dark:text-gray-300">Taller:</span>
                    {{ $trabajo->taller->nombre ?? 'N/A' }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Fecha Ingreso:</span>
                    {{ $trabajo->fecha_ingreso }}
                </p>
                <p class="text-gray-800 dark:text-gray-300">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Fecha Salida:</span>
                    {{ $trabajo->fecha_salida ?? 'Pendiente' }}
                </p>
                <div>
                    <p class="font-medium text-gray-800 dark:text-gray-300">Descripción del Servicio:</p>
                    <p class="text-gray-800 dark:text-gray-300 mt-1">{{ $trabajo->descripcion_servicio }}</p>
                </div>
            </div>
        </x-filament::card>

        <x-filament::card>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-4">
                Mecánicos Asignados
            </h2>
            <ul class="list-disc list-inside">
                @forelse($trabajo->usuarios as $usuario)
                    <li class="text-gray-800 dark:text-gray-300">{{ $usuario->name }}</li>
                @empty
                    <p class="text-gray-500 dark:text-gray-400">No hay mecánicos asignados.</p>
                @endforelse
            </ul>
        </x-filament::card>
    </div>

    <h2 class="text-xl font-bold">Evidencias Asociadas</h2>
    @if ($evidencias->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($evidencias as $evidencia)
                <x-filament::card>
                    @if ($evidencia->tipo === 'imagen')
                        <a href="{{ Storage::url($evidencia->evidencia_url) }}" data-fancybox="gallery"
                            data-caption="Actualizado el: {{ $evidencia->updated_at->format('d/m/Y H:i') }}">
                            <img src="{{ Storage::url($evidencia->evidencia_url) }}" alt="Evidencia"
                                class="w-full h-48 object-cover rounded-lg transition-transform transform hover:scale-105">
                        </a>
                    @elseif ($evidencia->tipo === 'video')
                        <video controls class="w-full h-48 rounded-lg">
                            <source src="{{ Storage::url($evidencia->evidencia_url) }}" type="video/mp4">
                            Tu navegador no soporta videos.
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
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $evidencia->observacion }}</p>
                    </div>
                </x-filament::card>
            @endforeach
        </div>
    @else
        <x-filament::card>
            <div class="flex flex-col items-center justify-center py-5 my-5">
                <svg style="max-width: 80px" xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mb-4"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                </svg>
                <p class="text-gray-500">Aún no se han subido fotos ni videos.</p>
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
