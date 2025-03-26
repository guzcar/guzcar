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
                            <td style="width: 80px">Placa</td>
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

    <h3>SERVICIOS</h3>

    <table class="table-container">
        <thead>
            <tr>
                <th style="width: 80px">Cantidad</th>
                <th>Descripción</th>
                <th style="width: 95px">Costo</th>
                <th style="width: 100px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trabajo->servicios as $trabajoServicio)
                <tr>
                    <td style="text-align: center">{{ $trabajoServicio->cantidad }}</td>
                    <td>
                        <p style="margin: 0px;"><b>{{ $trabajoServicio->servicio->nombre }}</b></p>
                        <p style="margin: 0px;">{{ $trabajoServicio->detalle }}</p>
                    </td>
                    <td style="text-align: right">S/ {{ $trabajoServicio->precio }}</td>
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
                <th style="width: 80px">Cantidad</th>
                <th>Descripción</th>
                <th style="width: 95px">Costo</th>
                <th style="width: 100px">Sub-Total</th>
            </tr>
        </thead>
        <tbody>
            @if($articulosAgrupados->isNotEmpty()) <!-- Validar si hay artículos -->
                    @foreach($articulosAgrupados as $articulo)
                            <tr>
                                <td style="text-align: center">
                                    {{ \App\Services\FractionService::decimalToFraction($articulo['cantidad']) }}
                                </td>
                                <td>
                                    @php
                                        $articuloData = $articulo['articulo'];
                                        $categoria = $articuloData->categoria->nombre ?? null;
                                        $marca = $articuloData->marca->nombre ?? null;
                                        $subCategoria = $articuloData->subCategoria->nombre ?? null;
                                        $especificacion = $articuloData->especificacion ?? null;
                                        $presentacion = $articuloData->presentacion->nombre ?? null;
                                        $medida = $articuloData->medida ?? null;
                                        $unidad = $articuloData->unidad->nombre ?? null;
                                        $color = $articuloData->color ?? null;

                                        $labelParts = [];
                                        if ($categoria)
                                            $labelParts[] = $categoria;
                                        if ($marca)
                                            $labelParts[] = $marca;
                                        if ($subCategoria)
                                            $labelParts[] = $subCategoria;
                                        if ($especificacion)
                                            $labelParts[] = $especificacion;
                                        if ($presentacion)
                                            $labelParts[] = $presentacion;
                                        if ($medida)
                                            $labelParts[] = $medida;
                                        if ($unidad)
                                            $labelParts[] = $unidad;
                                        if ($color)
                                            $labelParts[] = $color;

                                        echo implode(' ', $labelParts);
                                    @endphp
                                </td>
                                <td style="text-align: right">S/ {{ $articulo['precio'] }}</td>
                                <td style="text-align: right">S/
                                    {{ number_format($articulo['cantidad'] * $articulo['precio'], 2, '.', '') }}
                                </td>
                            </tr>
                    @endforeach
            @endif

            <!-- Sección para trabajo_otros -->
            @forelse($trabajo->otros as $trabajoOtro)
                <tr>
                    <td style="text-align: center">{{ $trabajoOtro->cantidad }}</td>
                    <td>{{ $trabajoOtro->descripcion }}</td>
                    <td style="text-align: right">S/ {{ $trabajoOtro->precio }}</td>
                    <td style="text-align: right">S/
                        {{ number_format($trabajoOtro->cantidad * $trabajoOtro->precio, 2, '.', '') }}
                    </td>
                </tr>
            @empty
                @if($articulosAgrupados->isEmpty()) <!-- Si no hay artículos ni trabajo_otros -->
                    <tr>
                        <td class="empty-case"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endif
            @endforelse

            @if($articulosAgrupados->isNotEmpty() || $trabajo->otros->isNotEmpty())
                <tr>
                    <td colspan="3" style="border: 0"></td>
                    <td style="text-align: right"><b>S/
                            {{ number_format($subtotal_articulos + $subtotal_trabajo_otros, 2, '.', '') }}</b></td>
                </tr>
            @endif
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
                    S/ {{ number_format($total_con_igv = $total * (1 + request('igv_porcentaje') / 100), 2, '.', '') }}</th>
            </tr>
        @else
            <tr>
                <td style="border: 0"></td>
                <td style="border: 0; width: 95px;">Total:</td>
                <th style="width: 100px; text-align: right;">S/ {{ number_format($total_con_igv = $total, 2, '.', '') }}
                </th>
            </tr>
        @endif
    </table>

    @php
        // Usamos $total_con_igv que ya contiene el monto correcto (con o sin IGV)
        $numberToWords = new NumberToWords\NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es');

        // Separar parte entera y decimal
        $entero = floor($total_con_igv);
        $decimal = round(($total_con_igv - $entero) * 100);

        // Convertir a palabras y formatear
        $palabras = strtoupper($numberTransformer->toWords($entero)) . " CON $decimal/100 SOLES";
    @endphp

    <p>SON: {{ $palabras }}</p>

    <p>Tiempo de ejecución: <b>{{ $tiempo }}</b></p>

</x-pdf-layout>