<x-pdf-layout title="Presupuesto {{ $trabajo->codigo }}" code="{{ $trabajo->codigo }}">

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
                <th style="width: 40px">N°</th>
                <th>Descripción</th>
                <th style="width: 80px">Cantidad</th>
                <th style="width: 100px">Costo</th>
                <th style="width: 100px">Sub-Total</th>
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
                <th style="width: 40px">N°</th>
                <th>Descripción</th>
                <th style="width: 80px">Cantidad</th>
                <th style="width: 100px">Costo</th>
                <th style="width: 100px">Sub-Total</th>
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
                <td style="padding: 0; margin: 0; border: none; width: 180px; vertical-align: top;">
                    @if($descuentos->isNotEmpty())
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Descuento</th>
                                    <th style="width: 100px;">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($descuentos as $descuento)
                                    <tr>
                                        <td class="text-center">{{ $descuento->descuento }}%</td>
                                        <td class="text-right">S/ {{ number_format($total * ($descuento->descuento / 100), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="border-top"></td>
                                    <td class="text-right border-top bold">S/ {{ number_format($total_descuentos, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    @endif
                </td>
                <td></td>
                <td style="width: 200px; vertical-align: top;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Resumen</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sub-Total</td>
                                <td class="text-right">S/ {{ number_format($total, 2) }}</td>
                            </tr>
                            @if($descuentos->isNotEmpty())
                                <tr>
                                    <td>Descuento</td>
                                    <td class="text-right">S/ {{ number_format($total_descuentos, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Sub-Total c/Desc</td>
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
                                <td class="border-top"></td>
                                <td class="text-right bold border-top">S/
                                    {{ number_format($total_con_igv = $total_con_descuentos * (1 + (request('igv') ? request('igv_porcentaje') / 100 : 0)), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    @php
        $numberToWords = new NumberToWords\NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es');
        $entero = floor($total_con_igv);
        $decimal = round(($total_con_igv - $entero) * 100);
        $palabras = strtoupper($numberTransformer->toWords($entero)) . " CON $decimal/100 SOLES";
    @endphp

    <div style="border-bottom: solid gray 1px; border-top: solid gray 1px; margin-top: 1rem;">
        <p>Son: <span class="bold">{{ $palabras }}</span></p>
    </div>

    <!-- <div style="border-top: dashed black 1px;"></div> -->

    <!-- <table style="border-collapse: collapse; width: 100%;">
        <tbody>
            <tr>
                <td style="padding: 0; margin: 0; border: none; vertical-align: top;">
                    <p style="margin-bottom: 0px;"><b>CUENTA BCP</b></p>
                    <ul style="padding-left: 0; list-style-position: inside; margin-top: 0px;">
                        <li>N° CTA: 3104600455054</li>
                        <li>CCI: 00231000460045505410</li>
                    </ul>
                </td>
                <td style="padding: 0; margin: 0; border: none; vertical-align: top;">
                    <p style="margin-bottom: 0px;"><b>CUENTA BBVA</b></p>
                    <ul style="padding-left: 0; list-style-position: inside; margin-top: 0px;">
                        <li>N° CTA: 001102950100124365</li>
                        <li>CCI: 0011029500010012436536</li>
                    </ul>
                </td>
                <td style="padding: 0; margin: 0; border: none; vertical-align: top;">
                    <p style="margin-bottom: 0px;"><b>BANCO DE LA NACION</b></p>
                    <ul style="padding-left: 0; list-style-position: inside; margin-top: 0px;">
                        <li>CTA. detracciones: 007850151</li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table> -->

    <table style="border-collapse: collapse; width: 100%; margin-top: 1rem;">
        <tbody>
            <tr>
                <td style="padding: 0; margin: 0; border: none; vertical-align: top; width: 45%; padding-right: 1rem;">
                    <p class="mt-0 mb-0">Tiempo de ejecución: <span class="bold">{{ $tiempo }}</span></p>

                    @if ($trabajo->garantia)
                        <p class="mt-0">Garantía: <span class="bold">{{ $trabajo?->garantia ?? '' }}</span></p>
                    @endif

                    @if ($trabajo->observaciones)
                        <!-- <div style="border-top: dashed black 1px;"></div> -->
                        <p class="mb-0">Observaciones:</p>
                        <p class="mt-0">{!! $trabajo->observaciones !!}</p>
                    @endif
                </td>
                <td style="padding: 0; margin: 0; border: none; vertical-align: top;">
                    <table class="table-simple">
                        <thead>
                            <tr>
                                <th style="width: 4rem;">Entidad</th>
                                <th>Tipo de Cuenta</th>
                                <th>Número de Cuenta</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td rowspan="2"><b>BCP</b></t>
                                <td style="text-align: center;">CTE</td>
                                <td>3104600455054</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">CCI</td>
                                <td>00231000460045505410</td>
                            </tr>
                            <tr>
                                <td rowspan="2"><b>BBVA</b></t>
                                <td style="text-align: center;">CTE</td>
                                <td>001102950100124365</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">CCI</td>
                                <td>0011029500010012436536</td>
                            </tr>
                            <tr>
                                <td><b>Banco de la Nación</b></t>
                                <td style="text-align: center;">Detracciones</td>
                                <td>007850151</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

</x-pdf-layout>