<x-pdf-layout 
    title="Acta Entrega #{{ $entrega->id }}" 
    code="{{ $entrega->maleta->codigo }}" 
    tipoReporte="ACTA DE ENTREGA {{ $soloSeleccionados ? '(PARCIAL)' : '' }}" 
    footer="AUTOMOTORES GUZCAR S.A.C.">

    <h1>ACTA DE ENTREGA - MALETA DE HERRAMIENTAS</h1>

    <ol style="text-transform: none; font-size: 13px; margin-left: -1rem;">

        <li>
            <p><b>INFORMACIÓN GENERAL</b></p>
            <ul style="margin-left: -1rem; list-style-type: disc">
                {{-- Usamos los datos guardados en la entrega, no los actuales del usuario --}}
                <li><b>Recibe (Propietario):</b> {{ $entrega->propietario->name ?? '—' }}</li>
                <li><b>Entrega (Responsable):</b> {{ $entrega->responsable->name ?? '—' }}</li>
                <li><b>Fecha de Emisión:</b> {{ $generatedAt->translatedFormat('d \d\e F \d\e\l Y') }}</li>
                <li><b>Fecha de Registro:</b> {{ $entrega->fecha->translatedFormat('d/m/Y') }}</li>
                <li><b>Codificación Maleta:</b> {{ $entrega->maleta->codigo ?? '—' }}</li>
            </ul>
        </li>

        <li>
            <p><b>DECLARACIÓN DE ENTREGA</b></p>
            <p>{{ $contenidoInforme ?? 'Por la presente se hace constancia de la entrega...' }}</p>
        </li>

        <li>
            <p><b>INFORMACIÓN DE HERRAMIENTAS {{ $soloSeleccionados ? '(SELECCIÓN)' : '' }}</b></p>
            <p>Total listado: {{ $totalHerramientas }}</p>

            @if($herramientas->isEmpty())
                <p>No hay herramientas registradas en esta selección.</p>
            @else
                <ul style="margin-left: -1rem; list-style-type: disc">
                    @foreach($herramientas as $herramienta)
                        <li>{{ $herramienta }}</li>
                    @endforeach
                </ul>
            @endif
        </li>
    </ol>

    <br><br><br>

    <table style="border-collapse: collapse; width: 100%; font-size: 13px;">
        <tr>
            <td style="width: 4%; padding-top: 50px;"></td>
            <td style="width: 36%; border-bottom: 2px solid black;"></td>
            <td style="width: 20%;"></td>
            <td style="width: 36%; border-bottom: 2px solid black;"></td>
            <td style="width: 4%;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-top: 5px; text-align: center;">
                <p><b>RECIBÍ CONFORME</b></p>
                <p style="margin: 0;">{{ $entrega->propietario->name ?? '' }}</p>
                <p style="margin: 0; font-size: 11px;">DNI: {{ $entrega->propietario->dni ?? '___________' }}</p>
            </td>
            <td></td>
            <td style="padding-top: 5px; text-align: center;">
                <p><b>ENTREGUE CONFORME</b></p>
                <p style="margin: 0;">{{ $entrega->responsable->name ?? '' }}</p>
                <p style="margin: 0; font-size: 11px;">DNI: {{ $entrega->responsable->dni ?? '___________' }}</p>
            </td>
            <td></td>
        </tr>
    </table>
</x-pdf-layout>