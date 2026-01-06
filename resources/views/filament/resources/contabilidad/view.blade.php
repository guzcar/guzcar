<x-filament::page>

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

        {{-- NUEVO BLOQUE: Detalle Técnico del Vehículo --}}
        <x-filament::section class="fi-section-content-p2">
            <x-slot name="heading">Detalle Técnico del Vehículo</x-slot>

            <div class="overflow-x-auto">
                <table class="w-full table-auto text-left border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                    <tbody>
                        {{-- VIN --}}
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300 w-40"
                                style="width: 120px;">Vin/Chasis</th>
                            <td class="px-4 py-2">
                                @php $vin = $trabajo->vehiculo->vin ?? null; @endphp
                                @if(!empty($vin))
                                    {{ $vin }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin VIN</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Motor --}}
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Motor</th>
                            <td class="px-4 py-2">
                                @php $motor = $trabajo->vehiculo->motor ?? null; @endphp
                                @if(!empty($motor))
                                    {{ $motor }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin N° Motor</span>
                                @endif
                            </td>
                        </tr>

                        {{-- Año --}}
                        <tr>
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Año</th>
                            <td class="px-4 py-2">
                                @php $ano = $trabajo->vehiculo->ano ?? null; @endphp
                                @if(!empty($ano))
                                    {{ $ano }}
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Sin año</span>
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

        {{-- Archivos --}}
        <x-filament::section>
            <x-slot name="heading">Archivos</x-slot>
            <ul class="list-disc list-inside">
                @forelse(($trabajo->archivos ?? []) as $archivo)
                    @php $ruta = $archivo->archivo_url ?? null; @endphp
                    @if(!empty($ruta))
                        <li>
                            <a class="font-medium text-primary-600 dark:text-primary-500 max-w-full truncate"
                                style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap; text-decoration: none;"
                                onmouseover="this.style.textDecoration='underline';"
                                onmouseout="this.style.textDecoration='none';" href="{{ asset('storage/' . $ruta) }}"
                                target="_blank" title="{{ basename($ruta) }}">
                                {{ basename($ruta) }}
                            </a>
                        </li>
                    @else
                        <li><span class="text-gray-500 dark:text-gray-400">Archivo inválido</span></li>
                    @endif
                @empty
                    <p class="text-gray-500 dark:text-gray-400">Sin archivos</p>
                @endforelse
            </ul>
        </x-filament::section>
    </div>

    {{-- Información Contable Adicional --}}
    <h2 class="text-xl font-bold mt-8">Información Contable</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Resumen Financiero --}}
        <x-filament::section class="fi-section-content-p2">
            <x-slot name="heading">
                Resumen Financiero
            </x-slot>
            
            <div class="overflow-x-auto">
                <table class="w-full table-auto text-left">
                    <tbody>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Importe</th>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                S/ {{ number_format((float)($trabajo->importe ?? 0), 2) }}
                            </td>
                        </tr>
                        
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">IGV</th>
                            <td class="px-4 py-2">
                                @if($trabajo->igv)
                                    <span class="text-green-600 dark:text-green-400">S/ {{ $trabajo->importe * 0.18 }}</span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">No aplica</span>
                                @endif
                            </td>
                        </tr>
                        
                        @if($trabajo->igv)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Importe con IGV</th>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                @php
                                    $importe = (float)($trabajo->importe ?? 0);
                                    $importeConIgv = $trabajo->igv ? ($importe * 1.18) : $importe;
                                @endphp
                                S/ {{ number_format($importeConIgv, 2) }}
                            </td>
                        </tr>
                        @endif
                        
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">A cuenta</th>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                S/ {{ number_format((float)($trabajo->a_cuenta ?? 0), 2) }}
                            </td>
                        </tr>
                        
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Por cobrar</th>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                S/ {{ number_format($trabajo->getPorCobrar(), 2) }}
                            </td>
                        </tr>
                        
                        <tr>
                            <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Desembolso</th>
                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                {{ $trabajo->desembolso ?? 'SIN COBRO' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Comprobantes --}}
        <x-filament::section class="fi-section-content-p2">
            <x-slot name="heading">
                Comprobantes
            </x-slot>
            
            <div class="overflow-x-auto">
                @if($trabajo->comprobantes && $trabajo->comprobantes->count() > 0)
                    <table class="w-full table-auto text-left">
                        <thead>
                            <tr class="border-b border-gray-300 dark:border-gray-600">
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 145px;">Código</th>
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Emisión</th>
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 135px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trabajo->comprobantes as $comprobante)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">
                                        @if($comprobante->url)
                                            <a href="{{ asset('storage/' . $comprobante->url) }}" 
                                               target="_blank"
                                               class="text-primary-600 dark:text-primary-500 hover:underline flex items-center">
                                                {{ $comprobante->codigo }}
                                            </a>
                                        @else
                                            {{ $comprobante->codigo }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $comprobante->emision->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2">S/ {{ number_format((float)$comprobante->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 dark:text-gray-400 p-4">No hay comprobantes registrados</p>
                @endif
            </div>
        </x-filament::section>
    </div>

    <div class="grid grid-cols-1 gap-6">
        {{-- Pagos --}}
        <x-filament::section class="fi-section-content-p2">
            <x-slot name="heading">
                Pagos
            </x-slot>
            
            <div class="overflow-x-auto">
                @if($trabajo->pagos && $trabajo->pagos->count() > 0)
                    <table class="w-full table-auto text-left">
                        <thead>
                            <tr class="border-b border-gray-300 dark:border-gray-600">
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Fecha</th>
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 135px;">Monto</th>
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Detalle</th>
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trabajo->pagos as $pago)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2">S/ {{ number_format((float)$pago->monto, 2) }}</td>
                                    <td class="px-4 py-2">
                                        @if($pago->detalle)
                                            {{ $pago->detalle->nombre ?? 'N/A' }}
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">Sin detalle</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2">{{ $pago->observacion ?? 'Sin observación' }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 dark:bg-gray-800 font-semibold">
                                <td class="px-4 py-2">Total</td>
                                <td class="px-4 py-2">S/ {{ number_format((float)$trabajo->pagos->sum('monto'), 2) }}</td>
                                <td class="px-4 py-2" colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 dark:text-gray-400 p-4">No hay pagos registrados</p>
                @endif
            </div>
        </x-filament::section>

        {{-- Descuentos --}}
        <x-filament::section class="fi-section-content-p2">
            <x-slot name="heading">
                Descuentos
            </x-slot>
            
            <div class="overflow-x-auto">
                @if(!empty($descuentos_calculados) && count($descuentos_calculados) > 0)
                    <table class="w-full table-auto text-left">
                        <thead>
                            <tr class="border-b border-gray-300 dark:border-gray-600">
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Detalle</th>
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300">Descuento</th>
                                <th class="px-4 py-2 font-medium text-gray-700 dark:text-gray-300" style="min-width: 135px;">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($descuentos_calculados as $dc)
                                @php $descuento = $dc['descuento']; @endphp
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-2">{{ $descuento->detalle ?? 'Sin Detalle' }}</td>
                                    <td class="px-4 py-2">{{ number_format((float)$descuento->descuento, 2) }}%</td>
                                    <td class="px-4 py-2">S/ {{ number_format($dc['monto'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50 dark:bg-gray-800 font-semibold">
                                <td colspan="2" class="px-4 py-2">Total Descuentos</td>
                                <td class="px-4 py-2">S/ {{ number_format($total_descuentos, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-4 py-2">Total Base (Sin Dscto.)</td>
                                <td class="px-4 py-2">S/ {{ number_format($total_base, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="px-4 py-2">Total con Dscto.</td>
                                <td class="px-4 py-2">S/ {{ number_format($total_con_descuentos, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 dark:text-gray-400 p-4">No hay descuentos registrados</p>
                @endif
            </div>
        </x-filament::section>
    </div>

</x-filament::page>