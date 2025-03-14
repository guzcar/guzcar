<x-pdf-layout title="Venta {{ $venta->codigo }}" code="{{ $venta->codigo }}">

    <h3>COMPROBANTE DE VENTA</h3>

    <h3 style="font-weight: normal;">CLIENTE</h3>

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top; padding-right: 0.5rem">
                <table class="table-container">
                    <tbody>
                        <tr>
                            <th style="width: 5rem; text-align: left;">RUC / DNI</th>
                            <td>{{ $venta->cliente->identificador }}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Cliente</th>
                            <td>{{ $venta->cliente->nombre }}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Telefono</th>
                            <td>{{ $venta->cliente->telefono }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td style="width: 32%; vertical-align: top">
                <table class="table-container">
                    <tbody>
                        <tr>
                            <th style="text-align: left;">Fecha</th>
                            <td style="width: 130px;">{{ $venta->fecha->format('d / m / Y') }}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Hora</th>
                            <td>{{ $venta->hora->format('H:i A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>


    @if ($venta->vehiculo)

        <h3 style="font-weight: normal;">VEHÍCULO</h3>

        <table style="width: 50%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top; padding-right: 0.5rem">
                    <table class="table-container">
                        <tbody>
                            <tr>
                                <th style="text-align: left; width: 5rem">Placa</th>
                                <td>{{ $venta->vehiculo->placa }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;">Marca</th>
                                <td>{{ $venta->vehiculo->marca }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;">Modelo</th>
                                <td>{{ $venta->vehiculo->modelo }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

    @endif

    <h3>ARTÍCULOS</h3>

    <table class="table-container">
        <thead>
            <tr>
                <th>Artículo</th>
                <th style="width: 95px">Costo</th>
                <th style="width: 95px">Cantidad</th>
                <th style="width: 100px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($venta->ventaArticulos as $ventaArticulo)
                <tr>
                    <td>
                        {{ $ventaArticulo->articulo->subCategoria->categoria->nombre }}
                        {{ $ventaArticulo->articulo->subCategoria->nombre }}
                        {{ $ventaArticulo->articulo->especificacion }}
                        {{ $ventaArticulo->articulo->marca }}
                        {{ $ventaArticulo->articulo->color }} -
                        {{ $ventaArticulo->articulo->tamano_presentacion }}
                    </td>
                    <td style="text-align: right">S/ {{ $ventaArticulo->precio }}</td>
                    <td style="text-align: center">{{ $ventaArticulo->cantidad }}</td>
                    <td style="text-align: right">S/
                        {{ number_format($ventaArticulo->cantidad * $ventaArticulo->precio, 2, '.', '') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="empty-case"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3></h3>

    <table class="table-container">
        @if (request('igv'))
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0; width: 95px;">Sub-Total:</td>
                <td style="text-align: right;">S/ {{ number_format($subtotal, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0; width: 95px;">IGV ({{ request('igv_porcentaje')}}%):</td>
                <td style="text-align: right;">S/
                    {{ number_format($subtotal * (request('igv_porcentaje') / 100), 2, '.', '') }}
                </td>
            </tr>
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0; width: 95px;">Total:</td>
                <th style="width: 100px; text-align: right;">S/
                    {{ number_format($subtotal * (1 + request('igv_porcentaje') / 100), 2, '.', '') }}
                </th>
            </tr>
        @else
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0; width: 95px;">Total:</td>
                <th style="width: 100px; text-align: right;"><b>S/ {{ number_format($subtotal, 2, '.', '') }}</b></th>
            </tr>
        @endif
    </table>

    @if ($venta->observacion)

        <h3>OBSERVACIONES</h3>
        <p>{{ $venta->observacion }}</p>
    @endif

</x-pdf-layout>