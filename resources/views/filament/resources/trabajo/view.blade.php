<x-filament::page>
    
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css"
/>

    <x-filament::card>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white pb-6">
            Información del Trabajo
        </h2>
        <div class="space-y-6">

            <hr class="border-gray-200 dark:border-gray-700">

            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Vehículo
                </h3>
                <p class="text-base text-gray-700 dark:text-gray-400 mt-2">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Placa:</span>
                    {{ $trabajo->vehiculo->placa ?? '1' }}
                </p>
                <p class="text-base text-gray-700 dark:text-gray-400 mt-2">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Marca:</span>
                    {{ $trabajo->vehiculo->marca }}
                </p>
                <p class="text-base text-gray-700 dark:text-gray-400 mt-2">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Modelo:</span>
                    {{ $trabajo->vehiculo->modelo }}
                </p>
                <p class="text-base text-gray-700 dark:text-gray-400 mt-2">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Color:</span>
                    {{ $trabajo->vehiculo->color }}
                </p>
                <p class="text-base text-gray-700 dark:text-gray-400 mt-2">
                    <span class="font-medium text-gray-900 dark:text-gray-300">Tipo de vehículo:</span>
                    {{ $trabajo->vehiculo->tipoVehiculo->nombre }}
                </p>
                <p class="text-base text-gray-700 dark:text-gray-400 mt-2">
                    Propietarios:
                </p>
                <ul class="mt-2 space-y-2 list-disc list-inside">
                    @forelse($trabajo->vehiculo->clientes as $cliente)
                        <li class="text-base text-gray-700 dark:text-gray-400">
                            <span class="font-medium text-gray-900 dark:text-gray-300">{{ $cliente->nombre }}</span>
                        </li>
                    @empty
                        <p class="text-base text-gray-500 dark:text-gray-400">No hay propietarios asignados.</p>
                    @endforelse
                </ul>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Trabajo
                </h3>
                <div class="space-y-4 mt-2">
                    <p class="text-base text-gray-700 dark:text-gray-400">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Taller:</span>
                        {{ $trabajo->taller->nombre ?? 'N/A' }}
                    </p>
                    <p class="text-base text-gray-700 dark:text-gray-400">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Fecha Ingreso:</span>
                        {{ $trabajo->fecha_ingreso }}
                    </p>
                    <p class="text-base text-gray-700 dark:text-gray-400">
                        <span class="font-medium text-gray-900 dark:text-gray-300">Fecha Salida:</span>
                        {{ $trabajo->fecha_salida ?? 'Pendiente' }}
                    </p>
                    <div>
                        <p class="font-medium text-gray-700 dark:text-gray-400">
                            Descripción del Servicio:
                        </p>
                        <p
                            class="text-base text-gray-700 dark:text-gray-400 pl-4 border-l-2 border-gray-300 dark:border-gray-600">
                            {{ $trabajo->descripcion_servicio }}
                        </p>
                    </div>
                </div>
            </div>

            <hr class="border-gray-200 dark:border-gray-700">

            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                    Mecánicos Asignados
                </h3>
                <ul class="mt-2 space-y-2 list-disc list-inside">
                    @forelse($trabajo->usuarios as $usuario)
                        <li class="text-base text-gray-700 dark:text-gray-400">
                            <span class="font-medium text-gray-900 dark:text-gray-300">{{ $usuario->name }}</span>
                        </li>
                    @empty
                        <p class="text-base text-gray-500 dark:text-gray-400">No hay técnicos asignados.</p>
                    @endforelse
                </ul>
            </div>
        </div>
    </x-filament::card>

    <x-filament::card>
        <h2 class="text-2xl font-bold mb-4">Evidencias Asociadas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @forelse ($evidencias as $evidencia)
                <x-filament::card>
                    @if (Str::endsWith($evidencia->evidencia_url, ['.jpg', '.jpeg', '.png', '.gif']))
                        <img src="{{ Storage::url($evidencia->evidencia_url) }}" alt="Evidencia"
                            class="w-full h-48 object-cover rounded-lg">
                    @elseif (Str::endsWith($evidencia->evidencia_url, ['.mp4', '.webm', '.ogg']))
                        <video controls class="w-full h-48 rounded-lg">
                            <source src="{{ Storage::url($evidencia->evidencia_url) }}" type="video/mp4">
                            Tu navegador no soporta videos.
                        </video>
                    @endif
                    <p class="mt-2 text-base text-gray-700 dark:text-gray-400">{{ $evidencia->observacion }}</p>
                </x-filament::card>
            @empty
                <p class="text-base text-gray-500">Aún no se han subido fotos ni videos.</p>
            @endforelse
        </div>
    </x-filament::card>
</x-filament::page>
