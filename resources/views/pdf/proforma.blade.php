<x-pdf-layout title="Proforma {{ $trabajo->codigo }}" code="{{ $trabajo->codigo }}">

    <table class="table-simple">
        <tbody>
            <tr>
                <td style="width: 14%;">DNI / RUC:</td>
                <td style="width: 34%; border-bottom: dotted black 1px;">{{ $clientePrincipal?->identificador ?? '' }}</td>
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

    <table class="table-simple">
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
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->marca ?? '' }}</td>
                <td style="padding-left: 1rem;">AÑO:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->ano ?? '' }}</td>
            </tr>
            <tr>
                <td>MODELO:</td>
                <td style="border-bottom: dotted black 1px;">{{ $trabajo->vehiculo?->modelo ?? '' }}</td>
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
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="height: 15px;"></td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3>REPUESTOS MATERIALES Y OTROS</h3>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 40px">N°</th>
                <th>Descripción</th>
                <th style="width: 80px">Cantidad</th>
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
                                    $articuloData->color ?? null
                                ];
                                echo implode(' ', array_filter($labelParts));
                            @endphp
                        </td>
                        <td class="text-center">
                            {{ \App\Services\FractionService::decimalToFraction($articulo['cantidad']) }}
                        </td>
                    </tr>
                @endforeach
            @endif

            @forelse($trabajo->otros as $trabajoOtro)
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td>{{ $trabajoOtro->descripcion }}</td>
                    <td class="text-center">{{ $trabajoOtro->cantidad }}</td>
                </tr>
            @empty
                @if($articulosAgrupados->isEmpty())
                    <tr>
                        <td colspan="3" class="text-center" style="height: 15px;"></td>
                    </tr>
                @endif
            @endforelse
        </tbody>
    </table>

    <!-- <div style="border-top: dashed black 1px; margin-top: 1rem;"></div> -->

    <p class="mb-0">Tiempo de ejecución: <span class="bold">{{ $tiempo }}</span></p>
    
    @if ($trabajo->garantia)
        <p class="mt-0">Garantía: <span class="bold">{{ $trabajo?->garantia ?? '' }}</span></p>
    @endif

    @if ($trabajo->observaciones)
    <!-- <div style="border-top: dashed black 1px;"></div> -->
    <p class="mb-0">Observaciones:</p>
    <p class="mt-0">{!! $trabajo->observaciones !!}</p>
    @endif

</x-pdf-layout>