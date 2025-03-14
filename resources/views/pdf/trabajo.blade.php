<x-pdf-layout title="Proforma {{ $trabajo->codigo }}" code="{{ $trabajo->codigo }}">

    <h3>PROFORMA</h3>

    <!-- Tabla Principal para alinear Vehículo y Cliente en una línea -->
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <!-- Columna Vehículo -->
            <td style="width: 50%; vertical-align: top; padding-right: 0.5rem">
                <table class="table-container">
                    <thead>
                        <tr>
                            <th colspan="2">VEHÍCULO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 50px">Placa</td>
                            <td>{{ $vehiculo->placa }}</td>
                        </tr>
                        <tr>
                            <td>Marca</td>
                            <td>{{ $vehiculo->marca }}</td>
                        </tr>
                        <tr>
                            <td>Modelo</td>
                            <td>{{ $vehiculo->modelo }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>

            <!-- Columna Clientes -->
            <td style="width: 50%; vertical-align: top;">
                <table class="table-container">
                    <thead>
                        <tr>
                            <th colspan="3">CLIENTES</th>
                        </tr>
                        <tr>
                            <th style="width: 100px;">RUC / DNI</th>
                            <th style="width: 120px;">Nombre</th>
                            <th style="width: 100px;">Contacto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($vehiculo->clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->identificador }}</td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>{{ $cliente->telefono }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="empty-case"></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <h3>SERVICIOS EJECUTADOS</h3>

    <table class="table-container">
        <thead>
            <tr>
                <th>Servicio</th>
                <th style="width: 95px">Costo</th>
                <th style="width: 95px">Cantidad</th>
                <th style="width: 100px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trabajo->servicios as $trabajoServicio)
                <tr>
                    <td>{{ $trabajoServicio->servicio->nombre }}</td>
                    <td style="text-align: right">S/ {{ $trabajoServicio->precio }}</td>
                    <td style="text-align: center">{{ $trabajoServicio->cantidad }}</td>
                    <td style="text-align: right">S/
                        {{ number_format($trabajoServicio->cantidad * $trabajoServicio->precio, 2, '.', '') }}
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
            <tr>
                <td colspan="3" style="border: 0"></td>
                <td style="text-align: right"><b>S/ {{ number_format($subtotal_servicios, 2, '.', '') }}</b></td>
            </tr>
        </tbody>
    </table>

    <h3>REPUESTOS MATERIALES Y OTROS</h3>

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
            @forelse($articulosAgrupados as $articulo)
                <tr>
                    <td>
                        {{ $articulo['articulo']->subCategoria->categoria->nombre }}
                        {{ $articulo['articulo']->subCategoria->nombre }}
                        {{ $articulo['articulo']->especificacion }}
                        {{ $articulo['articulo']->marca }}
                        {{ $articulo['articulo']->color }} - {{ $articulo['articulo']->tamano_presentacion }}
                    </td>
                    <td style="text-align: right">S/ {{ $articulo['precio'] }}</td>
                    <td style="text-align: center">
                        {{ \App\Services\FractionService::decimalToFraction($articulo['cantidad']) }}
                    </td>
                    <td style="text-align: right">S/
                        {{ number_format($articulo['cantidad'] * $articulo['precio'], 2, '.', '') }}
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
            <tr>
                <td colspan="3" style="border: 0"></td>
                <td style="text-align: right"><b>S/ {{ number_format($subtotal_articulos, 2, '.', '') }}</b></td>
            </tr>
        </tbody>
    </table>

    <h3></h3>

    <table class="table-container">
        @if (request('igv'))
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0; width: 95px;">Sub-Total:</td>
                <td style="text-align: right;">S/ {{ number_format($total, 2, '.', '') }}</td>
            </tr>
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0;">IGV ({{ request('igv_porcentaje')}}%):</td>
                <td style="text-align: right;">S/ {{ number_format($total * request('igv_porcentaje') / 100, 2, '.', '') }}
                </td>
            </tr>
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0;">Total:</td>
                <th style="width: 100px; text-align: right;">
                    S/ {{ number_format($total * (1 + request('igv_porcentaje') / 100), 2, '.', '') }}</th>
            </tr>
        @else
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0; width: 95px;">Total:</td>
                <th style="width: 100px; text-align: right;">S/ {{ number_format($total, 2, '.', '') }}</th>
            </tr>
        @endif
    </table>

    {{-- Fin de la parte que quiero mejorar --}}

    <p>Tiempo de ejecución: <b>{{ $tiempo }}</b></p>

</x-pdf-layout>