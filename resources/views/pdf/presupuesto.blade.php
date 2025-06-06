<x-pdf-layout title="Presupuesto {{ $trabajo->codigo }}" code="{{ $trabajo->codigo }}" tipoReporte="PRESUPUESTO">

    <h3 class="mt-0">CLIENTE</h3>

    <table class="table-void">
        <tbody>
            <tr>
                <td style="width: 14%;">DNI / RUC:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $clientePrincipal?->identificador ?? '' }}
                </td>
                <td style="width: 18%; padding-left: 1rem;">TELÉFONO:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $clientePrincipal?->telefono ?? '' }}</td>
            </tr>
            <tr>
                <td>CLIENTE:</td>
                <td colspan="3" style="border-bottom: dotted black 1px;">{{ $clientePrincipal?->nombre ?? '' }}</td>
            </tr>
            <tr>
                <td>DIRECCIÓN:</td>
                <td colspan="3" style="border-bottom: dotted black 1px;">{{ $clientePrincipal?->direccion ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    <h3>DATOS DE LA UNIDAD</h3>

    <table class="table-void">
        <tbody>
            <tr>
                <td style="width: 14%;">PLACA:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->placa ?? '' }}</td>
                <td style="width: 18%; padding-left: 1rem;">VIN / CHASIS:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->vin ?? '' }}</td>
            </tr>
            <tr>
                <td>TIPO:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo->tipoVehiculo?->nombre ?? '' }}</td>
                <td style="padding-left: 1rem;">MOTOR:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->motor ?? '' }}</td>
            </tr>
            <tr>
                <td>MARCA:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->marca?->nombre ?? '' }}</td>
                <td style="padding-left: 1rem;">AÑO:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->ano ?? '' }}</td>
            </tr>
            <tr>
                <td>MODELO:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->modelo?->nombre ?? '' }}</td>
                <td style="padding-left: 1rem;">KILOMETRAJE:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo?->kilometraje ?? '' }}</td>
            </tr>
            <tr>
                <td>COLOR:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->color ?? '' }}</td>
                <td style="padding-left: 1rem;">F. ENTREGA:</td>
                <td style="border-bottom: dotted black 1px;">
                    {{ optional($trabajo?->fecha_salida)->format('d/m/Y') ?? '' }}</td>
            </tr>
            @if ($trabajo->conductor)
                <tr>
                    <td>CONDUCTOR:</td>
                    <td colspan="3" style="border-bottom: dotted black 1px;">{{ $trabajo->conductor?->nombre ?? '' }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <h3>SERVICIOS</h3>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px">N°</th>
                <th>Descripción</th>
                <th style="width: 60px">Cant.</th>
                <th style="width: 80px">Costo</th>
                <th style="width: 80px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trabajo->servicios as $index => $trabajoServicio)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <p class="m-0 bold">{{ $trabajoServicio->servicio->nombre }}</p>
                        <p class="m-0">{{ $trabajoServicio->detalle }}</p>
                    </td>
                    <td class="text-center">{{ $trabajoServicio->cantidad }}</td>
                    <td class="text-right">S/ {{ $trabajoServicio->precio }}</td>
                    <td class="text-right">S/
                        {{ number_format($trabajoServicio->cantidad * $trabajoServicio->precio, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="height: 15px;"></td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="border-top"></td>
                <td class="text-right border-top bold">S/ {{ number_format($subtotal_servicios, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <h3>REPUESTOS MATERIALES Y OTROS</h3>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px">N°</th>
                <th>Descripción</th>
                <th style="width: 60px">Cant.</th>
                <th style="width: 80px">Costo</th>
                <th style="width: 80px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 1; @endphp
            @if($articulosAgrupados->isNotEmpty())
                @foreach($articulosAgrupados as $articulo)
                    <tr>
                        <td class="text-center">{{ $counter++ }}</td>
                        <td>
                            @php
                                $articuloData = $articulo['articulo'];
                                $labelParts = [
                                    $articuloData->categoria->nombre ?? null,
                                    $articuloData->marca->nombre ?? null,
                                    $articuloData->subCategoria->nombre ?? null,
                                    $articuloData->especificacion ?? null,
                                    $articuloData->presentacion->nombre ?? null,
                                    $articuloData->medida ?? null,
                                    $articuloData->unidad->nombre ?? null,
                                    /*$articuloData->color ?? null*/
                                ];
                                echo implode(' ', array_filter($labelParts));
                            @endphp
                        </td>
                        <td class="text-center">
                            {{ \App\Services\FractionService::decimalToFraction($articulo['cantidad']) }}
                        </td>
                        <td class="text-right">S/ {{ $articulo['precio'] }}</td>
                        <td class="text-right">S/
                            {{ number_format($articulo['cantidad'] * $articulo['precio'], 2) }}
                        </td>
                    </tr>
                @endforeach
            @endif

            @forelse($trabajo->otros as $trabajoOtro)
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td>{{ $trabajoOtro->descripcion }}</td>
                    <td class="text-center">{{ $trabajoOtro->cantidad }}</td>
                    <td class="text-right">S/ {{ $trabajoOtro->precio }}</td>
                    <td class="text-right">S/
                        {{ number_format($trabajoOtro->cantidad * $trabajoOtro->precio, 2) }}
                    </td>
                </tr>
            @empty
                @if($articulosAgrupados->isEmpty())
                    <tr>
                        <td colspan="5" class="text-center" style="height: 15px;"></td>
                    </tr>
                @endif
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="border-top"></td>
                <td class="text-right border-top bold">S/
                    {{ number_format($subtotal_articulos + $subtotal_trabajo_otros, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <h3>RESUMEN</h3>

    <table style="border-collapse: collapse; width: 100%;">
        <tbody>
            <tr>
                <!-- Columna izquierda: Tiempo, Garantía, Observaciones -->
                <td style="width: 65%; vertical-align: top; padding-right: 1rem;">
                    <p class="mt-0 mb-0">Tiempo de ejecución: <span class="bold">{{ $tiempo }}</span></p>

                    @if ($trabajo->garantia)
                        <p class="mt-0 mb-0">Tiempo de garantía: <span class="bold">{{ $trabajo->garantia }}</span></p>
                    @endif

                    @if ($trabajo->observaciones)
                        <p class="mb-0">Observaciones:</p>
                        <p class="mt-0">{!! $trabajo->observaciones !!}</p>
                    @endif
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
                                <td style="width: 100px" class="text-right">S/ {{ number_format($total, 2) }}</td>
                            </tr>

                            @foreach($descuentos as $descuento)
                                <tr>
                                    <td>DESC. {{ $descuento->descuento }}%</td>
                                    <td class="text-right">S/ {{ number_format($total * ($descuento->descuento / 100), 2) }}
                                    </td>
                                </tr>
                            @endforeach

                            @if($descuentos->isNotEmpty())
                                <tr>
                                    <td>TOTAL C/DESC</td>
                                    <td class="text-right">S/ {{ number_format($total_con_descuentos, 2) }}</td>
                                </tr>
                            @endif

                            @if (request('igv'))
                                <tr>
                                    <td>IGV ({{ request('igv_porcentaje')}}%)</td>
                                    <td class="text-right">S/
                                        {{ number_format($total_con_descuentos * request('igv_porcentaje') / 100, 2) }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="border-top bold">
                                    @if (!request('igv'))
                                        No incluye IGV
                                    @else
                                        Total
                                    @endif
                                </td>
                                <td class="text-right border-top bold">
                                    S/
                                    {{ number_format($total_con_igv = $total_con_descuentos * (1 + (request('igv') ? request('igv_porcentaje') / 100 : 0)), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Texto en palabras -->
    @php
        $numberToWords = new NumberToWords\NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es');
        $entero = floor($total_con_igv);
        $decimal = round(($total_con_igv - $entero) * 100);
        $palabras = strtoupper($numberTransformer->toWords($entero)) . " CON $decimal/100 SOLES";
    @endphp

    <div style="border-top: solid gray 1px; border-bottom: solid gray 1px; margin-top: 1rem;">
        <p class="bold">SON: {{ $palabras }}</p>
    </div>


</x-pdf-layout>