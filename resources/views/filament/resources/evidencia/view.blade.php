<x-filament::page>

    <x-filament::card>
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
    </x-filament::card>

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