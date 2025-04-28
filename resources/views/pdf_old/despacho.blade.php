<x-pdf-layout title="Despacho {{ $despacho->codigo }}" code="{{ $despacho->codigo }}">

    <h3>DESPACHO</h3>

    <p>ACEPTACIÓN DE RESPONSABILIDAD POR SOLICITUD DE ARTÍCULOS DE INVENTARIO</p>

    <p>
        Por la presente, se deja constancia de que el operario {{ $despacho->tecnico->name }} ha realizado la
        solicitud de los siguientes artículos al encargado de almacén {{ $despacho->responsable->name }}, quien
        confirma haber entregado los mismos.
    </p>

    <p>Datos de solicitud:</p>

    <table style="width: 80%; border-collapse: collapse;">
        <tr>
            @if ($despacho->trabajo)
                <td style="vertical-align: top; padding-right: 0.5rem">
                    <table class="table-container">
                        <tbody>
                            <tr>
                                <th style="text-align:left; width: 5rem">Trabajo</th>
                                <td>{{ $despacho->trabajo->codigo }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;">Placa</th>
                                <td>{{ $despacho->trabajo->vehiculo->placa }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;">Marca</th>
                                <td>{{ $despacho->trabajo->vehiculo->marca?->nombre }}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left;">Modelo</th>
                                <td>{{ $despacho->trabajo->vehiculo->modelo?->nombre }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            @endif
            <td style="width: 35%; vertical-align: top">
                <table class="table-container">
                    <tbody>
                        <tr>
                            <th style="text-align: left; width: 3rem">Fecha</th>
                            <td>{{ $despacho->fecha->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Hora</th>
                            <td>{{ $despacho->hora->format('H:i A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            @if (!$despacho->trabajo)
                <td></td>
            @endif
        </tr>
    </table>

    <h3 style="margin-top: 1rem;">ARTÍCULOS</h3>

    <table class="table-container">
        <thead>
            <tr>
                <th style="width: 3rem;">N°</th>
                <th>Artículo</th>
                <th style="width: 5rem;">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
            @endphp
            @forelse ($despacho->trabajoArticulos as $trabajoArticulo)
                        <tr>
                            <td style="text-align: center;">{{ $i }}</td>
                            <td>
                                @php
                                    $articulo = $trabajoArticulo->articulo;

                                    $categoria = $articulo->categoria->nombre ?? null;
                                    $presentacion = $articulo->presentacion->nombre ?? null;
                                    $subCategoria = $articulo->subCategoria->nombre ?? null;
                                    $especificacion = $articulo->especificacion ?? null;
                                    $marca = $articulo->marca->nombre ?? null;
                                    $medida = $articulo->medida ?? null;
                                    $unidad = $articulo->unidad->nombre ?? null;
                                    $color = $articulo->color ?? null;

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
                            <td style="text-align: center;">
                                {{ \App\Services\FractionService::decimalToFraction($trabajoArticulo->cantidad) }}
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
            @empty
                <tr>
                    <td class="empty-case"></td>
                    <td></td>
                    <td></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($despacho->observacion)
        <h3>OBSERVACIÓN</h3>
        <p>{{ $despacho->observacion }}</p>
    @endif

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 12.5%; height: 7rem"></td>
            <td style="width: 30%;"></td>
            <td style="width: 15%;"></td>
            <td style="width: 30%;"></td>
            <td style="width: 12.5%;"></td>
        </tr>
        <tr>
            <td></td>
            <td style=" border-top: 1px solid black; text-align: center;">
                <p style="margin-bottom: 2px;"><b>{{ $despacho->responsable->name }}</b></p>
                <p style="margin: 2px;">Responsable de Almacén</p>
            </td>
            <td></td>
            <td style=" border-top: 1px solid black; text-align: center;">
                <p style="margin-bottom: 2px;"><b>{{ $despacho->tecnico->name }}</b></p>
                <p style="margin: 2px;">Trabajador</p>
            </td>
            <td></td>
        </tr>
    </table>

    {{-- <p style="margin-top: 3rem;">Automotores GUZCAR S.A.C.</p> --}}
</x-pdf-layout>