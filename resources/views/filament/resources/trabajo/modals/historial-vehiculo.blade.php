<div class="space-y-6">

    @if(isset($historialAgrupado) && count($historialAgrupado) > 0)
        @foreach($historialAgrupado as $fecha => $datos)
            <div class="rounded-xl border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm">
                
                <div class="bg-gray-100 dark:bg-white/10 px-4 py-3 border-b border-gray-200 dark:border-white/10 flex items-center gap-2">
                    <x-heroicon-m-calendar-days class="w-5 h-5 text-gray-500 dark:text-gray-400"/>
                    <span class="font-bold text-gray-900 dark:text-white">{{ $fecha }}</span>
                </div>
                
                <div class="divide-y divide-gray-200 dark:divide-white/5 bg-white dark:bg-gray-900">
                    
                    @if(count($datos['info_trabajos']) > 0)
                        <div class="px-4 py-2.5 bg-blue-100 dark:bg-blue-900/40 border-b border-blue-200 dark:border-blue-800/50 flex items-center gap-2">
                            <x-heroicon-s-wrench-screwdriver class="w-4 h-4 text-blue-700 dark:text-blue-300"/>
                            <h4 class="text-xs font-bold text-blue-900 dark:text-blue-100 uppercase tracking-wide">Detalle del Trabajo</h4>
                        </div>

                        @foreach($datos['info_trabajos'] as $info)
                            <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3">
                                    <div class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap flex-1">{{ $info['descripcion'] }}</div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300 font-medium sm:text-right bg-gray-100 dark:bg-white/10 px-3 py-1.5 rounded-lg inline-flex items-center gap-2 self-start border border-gray-200 dark:border-white/10">
                                        @if(is_null($info['kilometraje']))
                                            <span class="text-gray-400 italic font-normal text-xs uppercase tracking-wide">No reg.</span>
                                        @else
                                            {{ number_format($info['kilometraje'], 0, '.', ',') }} <span class="text-gray-500 text-xs">Km</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if(count($datos['elementos']) > 0)
                        
                        <div class="sm:hidden px-4 py-2.5 bg-indigo-100 dark:bg-indigo-900/40 border-b border-indigo-200 dark:border-indigo-800/50 flex items-center gap-2">
                            <x-heroicon-s-beaker class="w-4 h-4 text-indigo-700 dark:text-indigo-300"/>
                            <h4 class="text-xs font-bold text-indigo-900 dark:text-indigo-100 uppercase tracking-wide">Filtros, Aceites y Arandelas</h4>
                        </div>

                        <div class="hidden sm:grid sm:grid-cols-12 px-4 py-2.5 bg-indigo-100 dark:bg-indigo-900/40 border-b border-indigo-200 dark:border-indigo-800/50 text-xs font-bold tracking-wide text-indigo-900 dark:text-indigo-100 uppercase">
                            <div class="sm:col-span-8 flex items-center gap-2">
                                <x-heroicon-s-beaker class="w-4 h-4 text-indigo-700 dark:text-indigo-300"/>
                                Filtros, Aceites y Arandelas
                            </div>
                            <div class="sm:col-span-2 text-center">Cant. Usada</div>
                            <div class="sm:col-span-2 text-center">Stock Actual</div>
                        </div>
                        
                        @foreach($datos['elementos'] as $elemento)
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-y-2 sm:gap-4 px-4 py-3 items-center hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                                <div class="sm:col-span-8 text-sm text-gray-800 dark:text-gray-200">
                                    <span class="sm:hidden font-bold text-gray-500 text-xs uppercase block mb-1">Descripción</span>
                                    {{ $elemento['nombre'] }}
                                    
                                    @if($elemento['tipo'] === 'otro')
                                        <span class="ml-2 inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-white/5 dark:text-gray-400 dark:ring-white/10">
                                            Otro
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="sm:col-span-2 text-sm text-gray-700 dark:text-gray-300 flex justify-between sm:justify-center items-center">
                                    <span class="sm:hidden font-bold text-gray-500 text-xs uppercase">Cant. Usada</span>
                                    <span>{{ (float) $elemento['cantidad'] }}</span>
                                </div>
                                
                                <div class="sm:col-span-2 text-sm flex justify-between sm:justify-center items-center">
                                    <span class="sm:hidden font-bold text-gray-500 text-xs uppercase">Stock</span>
                                    
                                    @if($elemento['tipo'] === 'articulo')
                                        @if($elemento['stock'] > 0)
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-success-100 text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                                {{ (float) $elemento['stock'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 text-xs font-bold rounded-full bg-danger-100 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400">
                                                Agotado
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center justify-center text-gray-400 dark:text-gray-500" title="No aplica stock (Elemento externo)">
                                            <x-heroicon-m-no-symbol class="w-5 h-5"/>
                                        </span>
                                    @endif
                                    
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                </div>
            </div>
        @endforeach
    @else
        <div class="flex flex-col items-center justify-center p-8 bg-gray-50 dark:bg-white/5 rounded-xl border border-dashed border-gray-300 dark:border-white/20">
            <x-heroicon-o-document-magnifying-glass class="w-10 h-10 text-gray-400 mb-3"/>
            <p class="text-sm text-gray-500 dark:text-gray-400 italic text-center">
                No se encontraron mantenimientos, filtros, aceites o arandelas en el historial de este vehículo.
            </p>
        </div>
    @endif

</div>