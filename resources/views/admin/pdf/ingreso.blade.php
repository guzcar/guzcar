<x-pdf-layout title="Check List Ingreso de Vehículo" code="{{ $codigo }}" tipoReporte="CHECK LIST">

    <style>
        .checklist-item {
            margin-bottom: 3px;
        }

        .level-bar {
            width: 80%;
            height: 12px;
            border: 1px solid #000;
            background: #f0f0f0;
            margin: 5px 0;
        }

        .level-fill {
            height: 100%;
            background: #333;
        }

        .diagram-container {
            width: 100%;
            height: 300px;
            position: relative;
            margin: 0 auto;
            overflow: hidden;
        }

        .diagram-image {
            width: 100%;
            height: 100%;
        }

        .symbol {
            position: absolute;
            font-weight: bold;
            font-size: 16px;
            transform: translate(-50%, -50%);
        }

        .symbol-O {
            color: blue;
        }

        .symbol-X {
            color: red;
        }

        .symbol-slash {
            color: orange;
        }

        .signature-box {
            width: 100%;
            height: 60px;
        }
    </style>

    <h3>CLIENTE</h3>

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

    <h3>INVENTARIO DE VEHICULO</h3>

    @php
        $inventarioData = $trabajo->inventario_vehiculo_ingreso ?? [];
        $checklistItems = $inventarioData['checklist'] ?? [];
        $checkedItems = collect($checklistItems)->where('checked', true);
        $symbols = $inventarioData['symbols'] ?? [];
    @endphp

    <table class="table">
        <thead>
            <tr>
                <th width="50%">Inventario</th>
                <th width="50%">Diagrama</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="vertical-align: top;">
                    <!-- CHECKLIST -->
                    <h4>Items encontrados</h4>
                    @if($checkedItems->count() > 0)
                        @foreach($checkedItems as $item)
                            <div class="checklist-item">• {{ $item['cantidad'] }} × {{ $item['nombre'] }}</div>
                        @endforeach
                    @else
                        <div style="font-style: italic; color: #666;">No hay items</div>
                    @endif

                    <br>

                    <!-- NIVELES -->
                    <h4>COMBUSTIBLE</h4>
                    <div class="level-bar">
                        @php $combustible = $inventarioData['combustible'] ?? 0; @endphp
                        <div class="level-fill" style="width: {{ $combustible }}%;"></div>
                    </div>

                    <h4>ACEITE</h4>
                    <div class="level-bar" style="margin-bottom: 2rem;">
                        @php $aceite = $inventarioData['aceite'] ?? 0; @endphp
                        <div class="level-fill" style="width: {{ $aceite }}%;"></div>
                    </div>
                </td>
                <td style="vertical-align: top;">
                    @if($trabajo->vehiculo?->tipoVehiculo?->diagrama)
                        <div class="diagram-container">
                            <img src="{{ storage_path('app/public/' . $trabajo->vehiculo->tipoVehiculo->diagrama) }}"
                                class="diagram-image">
                            @foreach($symbols as $symbol)
                                @php
                                    $x = (float) str_replace('%', '', $symbol['x']);
                                    $y = (float) str_replace('%', '', $symbol['y']);
                                @endphp
                                <div class="symbol symbol-{{ $symbol['type'] === '//' ? 'slash' : $symbol['type'] }}"
                                    style="left: {{ $x }}%; top: {{ $y }}%;">
                                    {{ $symbol['type'] }}
                                </div>
                            @endforeach
                        </div>

                        <div style="margin: 1rem 0;">
                            <strong>Leyenda:</strong>
                            <ul>
                                <li>O - Abolladura</li>
                                <li>X - Quiñe</li>
                                <li>// - Rayadura</li>
                            </ul>
                        </div>
                    @else
                        <div style="font-style: italic; color: #666; margin-top: 20px;">Sin diagrama</div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table" style="margin-top: 1rem;">
        <tbody>
            <tr>
                <td style="vertical-align: top;" width="50%">
                    <h4>Observaciones</h4>
                    <p>{{ $inventarioData['observaciones'] ?? 'Ninguna' }}</p>
                </td>
                <td style="vertical-align: bottom; text-align: center;" width="50%">
                    <div
                        style="border: 1px; height: 150px; display: flex; flex-direction: column; justify-content: flex-end;">
                        <div class="signature-box"
                            style="border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 5px;">
                            @if(isset($inventarioData['firma']) && $inventarioData['firma'])
                                <img src="{{ $inventarioData['firma'] }}" style="max-width: 100%; max-height: 100%;"
                                    alt="Firma">
                            @endif
                        </div>
                        <div style="margin-top: 5px; font-size: 11px;">
                            <p>FIRMA DE CONFORMIDAD</p>
                            <p>{{ $clientePrincipal?->nombre ?? '' }}</p>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

</x-pdf-layout>