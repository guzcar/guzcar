<x-pdf-layout title="Cotización {{ $presupuesto->id }}" code="{{ $presupuesto->id }}" tipoReporte="COTIZACION">

    <h3 class="mt-0">CLIENTE</h3>

    <table class="table-void">
        <tbody>
            <tr>
                <td style="width: 14%;">DNI / RUC:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $presupuesto->cliente?->identificador ?? '' }}
                </td>
                <td style="width: 18%; padding-left: 1rem;">TELÉFONO:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $presupuesto->cliente?->telefono ?? '' }}</td>
            </tr>
            <tr>
                <td>CLIENTE:</td>
                <td colspan="3" style="border-bottom: dotted black 1px;">{{ $presupuesto->cliente?->nombre ?? '' }}</td>
            </tr>
            <tr>
                <td>DIRECCIÓN:</td>
                <td colspan="3" style="border-bottom: dotted black 1px;">{{ $presupuesto->cliente?->direccion ?? '' }}</td>
            </tr>
        </tbody>
    </table>

    <h3>DATOS DE LA UNIDAD</h3>

    <table class="table-void">
        <tbody>
            <tr>
                <td style="width: 14%;">PLACA:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $presupuesto->vehiculo?->placa ?? '' }}</td>
                <td style="width: 18%; padding-left: 1rem;">VIN / CHASIS:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $presupuesto->vehiculo?->vin ?? '' }}</td>
            </tr>
            <tr>
                <td>TIPO:</td>
                <td style="border-bottom: dotted black 1px;">{{ $presupuesto->vehiculo?->tipoVehiculo?->nombre ?? '' }}</td>
                <td style="padding-left: 1rem;">MOTOR:</td>
                <td style="border-bottom: dotted black 1px;">{{ $presupuesto->vehiculo?->motor ?? '' }}</td>
            </tr>
            <tr>
                <td>MARCA:</td>
                <td style="border-bottom: dotted black 1px;">{{ $presupuesto->vehiculo?->marca?->nombre ?? '' }}</td>
                <td style="padding-left: 1rem;">AÑO:</td>
                <td style="border-bottom: dotted black 1px;">{{ $presupuesto->vehiculo?->ano ?? '' }}</td>
            </tr>
            <tr>
                <td>MODELO:</td>
                <td style="border-bottom: dotted black 1px;">{{ $presupuesto->vehiculo?->modelo?->nombre ?? '' }}</td>
                <td style="padding-left: 1rem;">COLOR:</td>
                <td style="border-bottom: dotted black 1px;">{{ $presupuesto->vehiculo?->color ?? '' }}</td>
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
                <th style="width: 80px">Costo U.</th>
                <th style="width: 80px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presupuesto->servicios as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <p class="m-0">{{ $item->descripcion }}</p>
                    </td>
                    <td class="text-center">{{ $item->cantidad }}</td>
                    <td class="text-right">S/ {{ number_format($item->precio, 2) }}</td>
                    <td class="text-right">S/
                        {{ number_format($item->cantidad * $item->precio, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="height: 15px;">- Sin servicios -</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="border-top" style="text-align: right; border-right: none; font-weight: bold;">SUBTOTAL</td>
                <td class="text-right border-top bold">S/ {{ number_format($subtotal_servicios, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <h3>REPUESTOS / ARTÍCULOS</h3>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 30px">N°</th>
                <th>Descripción</th>
                <th style="width: 60px">Cant.</th>
                <th style="width: 80px">Costo U.</th>
                <th style="width: 80px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presupuesto->articulos as $index => $item)
                 <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <p class="m-0">{{ $item->descripcion }}</p>
                    </td>
                    <td class="text-center">{{ $item->cantidad }}</td> <td class="text-right">S/ {{ number_format($item->precio, 2) }}</td>
                    <td class="text-right">S/
                        {{ number_format($item->cantidad * $item->precio, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="height: 15px;">- Sin artículos -</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="border-top" style="text-align: right; border-right: none; font-weight: bold;">SUBTOTAL</td>
                <td class="text-right border-top bold">S/
                    {{ number_format($subtotal_articulos, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <h3>RESUMEN</h3>

    <table style="border-collapse: collapse; width: 100%;">
        <tbody>
            <tr>
                <td style="width: 65%; vertical-align: top; padding-right: 1rem;">
                    @if ($presupuesto->observacion)
                        <p class="mb-0 bold" style="margin-bottom: 10px;">Observaciones:</p>
                        <p class="mt-0">
                            {!! nl2br(e($presupuesto->observacion)) !!}
                        </p>
                    @endif
                </td>

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

                            @if ($presupuesto->igv) <tr>
                                    <td>IGV (18%)</td>
                                    <td class="text-right">S/
                                        {{ number_format($monto_igv, 2) }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="border-top bold">
                                    @if (!$presupuesto->igv)
                                        Total
                                    @else
                                        Total General
                                    @endif
                                </td>
                                <td class="text-right border-top bold">
                                    S/ {{ number_format($total_con_igv, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="border-top: solid gray 1px; border-bottom: solid gray 1px; margin-top: 1rem; padding: 5px 0;">
        <p class="bold m-0">SON: {{ $palabras }}</p>
    </div>

</x-pdf-layout>