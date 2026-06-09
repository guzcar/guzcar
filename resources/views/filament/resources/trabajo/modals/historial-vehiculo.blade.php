<div class="space-y-8">
    <div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <x-heroicon-o-beaker class="w-5 h-5 text-primary-500"/>
            Filtros y Aceites (Artículos)
        </h3>
        
        @if(count($articulosAgrupados) > 0)
            <div class="space-y-6">
                @foreach($articulosAgrupados as $fecha => $articulos)
                    <div class="rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm">
                        <div class="bg-gray-100 dark:bg-white/10 px-4 py-3 border-b border-gray-200 dark:border-white/10 flex items-center gap-2">
                            <x-heroicon-m-calendar-days class="w-5 h-5 text-gray-500 dark:text-gray-400"/>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $fecha }}</span>
                        </div>
                        
                        <div class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-gray-900">
                            <div class="hidden sm:grid sm:grid-cols-12 px-4 py-2 bg-gray-50/50 dark:bg-white/[0.02] text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">
                                <div class="sm:col-span-8"></div>
                                <div class="sm:col-span-2 text-center">Cant. Usada</div>
                                <div class="sm:col-span-2 text-center">Stock Actual</div>
                            </div>
                            
                            @foreach($articulos as $art)
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-y-2 sm:gap-4 px-4 py-3 items-center hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <div class="sm:col-span-8 text-sm text-gray-800 dark:text-gray-200">
                                        <span class="sm:hidden font-bold text-gray-500 text-xs uppercase block mb-1">Descripción</span>
                                        {{ $art['nombre'] }}
                                    </div>
                                    <div class="sm:col-span-2 text-sm text-gray-700 dark:text-gray-300 flex justify-between sm:justify-center items-center">
                                        <span class="sm:hidden font-bold text-gray-500 text-xs uppercase">Cant. Usada</span>
                                        <span>{{ (float) $art['cantidad'] }}</span>
                                    </div>
                                    <div class="sm:col-span-2 text-sm flex justify-between sm:justify-center items-center">
                                        <span class="sm:hidden font-bold text-gray-500 text-xs uppercase">Stock</span>
                                        @if($art['stock'] > 0)
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                                {{ (float) $art['stock'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400">
                                                Agotado
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 italic bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-dashed border-gray-300 dark:border-white/20">
                No se encontraron registros previos de filtros o aceites en inventario.
            </p>
        @endif
    </div>

    <div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <x-heroicon-o-puzzle-piece class="w-5 h-5 text-warning-500"/>
            Filtros y Aceites (Otros Elementos)
        </h3>
        
        @if(count($otrosAgrupados) > 0)
            <div class="space-y-6">
                @foreach($otrosAgrupados as $fecha => $otros)
                    <div class="rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm">
                        <div class="bg-gray-100 dark:bg-white/10 px-4 py-3 border-b border-gray-200 dark:border-white/10 flex items-center gap-2">
                            <x-heroicon-m-calendar-days class="w-5 h-5 text-gray-500 dark:text-gray-400"/>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $fecha }}</span>
                        </div>
                        
                        <div class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-gray-900">
                            <div class="hidden sm:grid sm:grid-cols-12 px-4 py-2 bg-gray-50/50 dark:bg-white/[0.02] text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">
                                <div class="sm:col-span-9">Descripción</div>
                                <div class="sm:col-span-3 text-center">Cant. Usada</div>
                            </div>
                            
                            @foreach($otros as $otro)
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-y-2 sm:gap-4 px-4 py-3 items-center hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                    <div class="sm:col-span-9 text-sm text-gray-800 dark:text-gray-200">
                                        <span class="sm:hidden font-bold text-gray-500 text-xs uppercase block mb-1">Descripción</span>
                                        {{ $otro['nombre'] }}
                                    </div>
                                    <div class="sm:col-span-3 text-sm text-gray-700 dark:text-gray-300 flex justify-between sm:justify-center items-center">
                                        <span class="sm:hidden font-bold text-gray-500 text-xs uppercase">Cant. Usada</span>
                                        <span>{{ (float) $otro['cantidad'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 italic bg-gray-50 dark:bg-white/5 p-4 rounded-lg border border-dashed border-gray-300 dark:border-white/20">
                No se encontraron registros previos en la categoría "Otros".
            </p>
        @endif
    </div>
</div>