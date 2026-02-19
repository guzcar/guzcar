<div x-data="{ 
    showModal: false, 
    showDropdown: false,
    hasSeen: sessionStorage.getItem('seen_events_timestamp'),
    init() {
        // Lógica: Mostrar modal si hay eventos y no se ha cerrado en esta sesión recientemente
        // O simplemente mostrarlo siempre que se recarga la página si hay eventos importantes
        @if($events->count() > 0)
            // Si quieres que aparezca solo una vez por sesión de navegador:
            // if (!sessionStorage.getItem('guzcar_events_seen')) {
                this.showModal = true;
            // }
        @endif
    },
    markAsSeen() {
        this.showModal = false;
        sessionStorage.setItem('guzcar_events_seen', 'true');
    }
}" class="relative">

    <button @click="showDropdown = !showDropdown" @click.outside="showDropdown = false" class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none">
        <span class="sr-only">Notificaciones</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>
        
        @if($events->count() > 0)
            <span class="absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
        @endif
    </button>

    <div x-show="showDropdown" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
         style="display: none;">
        
        <div class="px-4 py-2 border-b border-gray-100 font-semibold text-gray-700">
            Avisos Globales Activos
        </div>

        @forelse($events as $event)
            <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                <p class="text-sm font-medium text-gray-900">{{ $event->title }}</p>
                <p class="text-xs text-gray-500 mt-1">
                    Hasta: {{ $event->ends_at->format('d/m/Y H:i') }}
                </p>
            </div>
        @empty
            <div class="px-4 py-3 text-sm text-gray-500 text-center">
                No hay avisos activos.
            </div>
        @endforelse
    </div>

    @if($events->count() > 0)
    <div x-show="showModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden transform transition-all"
             @click.outside="markAsSeen()">
            
            <div class="bg-blue-600 px-4 py-3 flex justify-between items-center">
                <h3 class="text-lg font-medium text-white">Comunicado Importante</h3>
                <button @click="markAsSeen()" class="text-blue-100 hover:text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                @php $latestEvent = $events->first(); @endphp
                <h4 class="text-xl font-bold text-gray-800 mb-2">{{ $latestEvent->title }}</h4>
                <p class="text-sm text-gray-500 mb-4">
                    Fecha: {{ $latestEvent->ends_at->format('d/m/Y H:i') }}
                </p>
                
                @if($events->count() > 1)
                    <p class="text-xs text-gray-400 mt-4 italic">
                        Tienes {{ $events->count() - 1 }} aviso(s) más. Revisa la campanita.
                    </p>
                @endif
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse">
                <button type="button" @click="markAsSeen()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                    Entendido
                </button>
            </div>
        </div>
    </div>
    @endif

</div>