<x-pdf-layout 
    title="Cotización {{ $cotizacion->id }}" 
    code="COT-{{ str_pad($cotizacion->id, 6, '0', STR_PAD_LEFT) }}" 
    tipoReporte="COTIZACIÓN">

    <h3 class="mt-0">CLIENTE</h3>

    <table class="table-void">
        <tbody>
            <tr>
                <td style="width: 14%;">DNI / RUC:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">
                    {{ $cliente->identificador ?? '' }}
                </td>
                <td style="width: 18%; padding-left: 1rem;">TELÉFONO:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">
                    {{ $cliente->telefono ?? '' }}
                </td>
            </tr>
            <tr>
                <td>CLIENTE:</td>
                <td colspan="3" style="border-bottom: dotted black 1px;">
                    {{ $cliente->nombre ?? '' }}
                </td>
            </tr>
            <tr>
                <td>DIRECCIÓN:</td>
                <td colspan="3" style="border-bottom: dotted black 1px;">
                    {{ $cliente->direccion ?? '' }}
                </td>
            </tr>
        </tbody>
    </table>

    <h3>DATOS DE LA UNIDAD</h3>

    <table class="table-void">
        <tbody>
            <tr>
                <td style="width: 14%;">PLACA:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">
                    {{ $cotizacion->vehiculo->placa ?? '' }}
                </td>
                <td style="width: 18%; padding-left: 1rem;">VIN / CHASIS:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">
                    {{ $cotizacion->vehiculo->vin ?? '' }}
                </td>
            </tr>
            <tr>
                <td>TIPO:</td>
                <td style="border-bottom: dotted black 1px;">
                    {{ $cotizacion->vehiculo->tipoVehiculo->nombre ?? '' }}
                </td>
                <td style="padding-left: 1rem;">MOTOR:</td>
                <td style="border-bottom: dotted black 1px;">
                    {{ $cotizacion->vehiculo->motor ?? '' }}
                </td>
            </tr>
            <tr>
                <td>MARCA:</td>
                <td style="border-bottom: dotted black 1px;">
                    {{ $cotizacion->vehiculo->marca->nombre ?? '' }}
                </td>
                <td style="padding-left: 1rem;">AÑO:</td>
                <td style="border-bottom: dotted black 1px;">
                    {{ $cotizacion->vehiculo->ano ?? '' }}
                </td>
            </tr>
            <tr>
                <td>MODELO:</td>
                <td style="border-bottom: dotted black 1px;">
                    {{ $cotizacion->vehiculo->modelo->nombre ?? '' }}
                </td>
                <td style="padding-left: 1rem;">COLOR:</td>
                <td style="border-bottom: dotted black 1px;">
                    {{ $cotizacion->vehiculo->color ?? '' }}
                </td>
            </tr>
        </tbody>
    </table>

    <h3>SERVICIOS</h3>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px">N°</th>
                <th>Descripción</th>
                <th style="width: 60px">Cant.</th>
                <th style="width: 80px">Precio U.</th>
                <th style="width: 80px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cotizacion->servicios as $index => $servicio)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <p class="m-0 bold">{{ $servicio->servicio->nombre ?? 'Servicio no encontrado' }}</p>
                        @if($servicio->detalle)
                            <p class="m-0">{{ $servicio->detalle }}</p>
                        @endif
                    </td>
                    <td class="text-center">{{ $servicio->cantidad }}</td>
                    <td class="text-right">S/ {{ number_format($servicio->precio, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($servicio->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="height: 15px;">No hay servicios</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="border-top text-right bold">Subtotal Servicios:</td>
                <td class="text-right border-top bold">S/ {{ number_format($subtotal_servicios, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <h3>ARTÍCULOS Y REPUESTOS</h3>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px">N°</th>
                <th>Descripción</th>
                <th style="width: 60px">Cant.</th>
                <th style="width: 80px">Precio U.</th>
                <th style="width: 80px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cotizacion->articulos as $index => $articulo)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $articulo->descripcion }}</td>
                    <td class="text-center">{{ $articulo->cantidad }}</td>
                    <td class="text-right">S/ {{ number_format($articulo->precio, 2) }}</td>
                    <td class="text-right">S/ {{ number_format($articulo->total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="height: 15px;">No hay artículos</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="border-top text-right bold">Subtotal Artículos:</td>
                <td class="text-right border-top bold">S/ {{ number_format($subtotal_articulos, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <h3>RESUMEN</h3>

    <table style="border-collapse: collapse; width: 100%;">
        <tbody>
            <tr>
                <!-- Columna izquierda: Observaciones -->
                <td style="width: 65%; vertical-align: top; padding-right: 1rem;">
                    @if ($cotizacion->observacion)
                        <p class="mb-0">Observaciones:</p>
                        <p class="mt-0">{{ $cotizacion->observacion }}</p>
                    @endif
                    
                    <p class="mb-0">Fecha de cotización: 
                        <span class="bold">{{ $cotizacion->created_at->format('d/m/Y') }}</span>
                    </p>
                </td>

                <!-- Columna derecha: Resumen de montos -->
                <td style="width: 35%; vertical-align: top;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>DESCRIPCIÓN</th>
                                <th>MONTO</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SUBTOTAL</td>
                                <td style="width: 100px" class="text-right">S/ {{ number_format($subtotal, 2) }}</td>
                            </tr>

                            @if ($cotizacion->igv)
                                <tr>
                                    <td>IGV (18%)</td>
                                    <td class="text-right">S/ {{ number_format($igv_calculado, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="border-top bold">
                                    @if (!$cotizacion->igv)
                                        No incluye IGV
                                    @else
                                        Total
                                    @endif
                                </td>
                                <td class="text-right border-top bold">
                                    S/ {{ number_format($total, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Texto en palabras -->
    <div style="border-top: solid gray 1px; border-bottom: solid gray 1px; margin-top: 1rem;">
        <p class="bold">SON: {{ $palabras }}</p>
    </div>
</x-pdf-layout>