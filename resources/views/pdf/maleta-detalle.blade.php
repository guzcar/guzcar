<x-pdf-layout title="Maleta {{ $maleta->codigo }}" code="{{ $maleta->codigo }}" tipoReporte="ACTA DE ENTREGA" footer="AUTOMOTORES GUZCAR S.A.C.">

    <h1>ACTA DE ENTREGA - MALETA DE HERRAMIENTAS</h1>

    <ol style="text-transform: none; font-size: 13px; margin-left: -1rem;">
        <li>
            <p><b>INFORMACIÓN GENERAL</b></p>
            <ul style="margin-left: -1rem; list-style-type: disc">
                <li><b>Recibe:</b> {{ $maleta->propietario->name ?? '—' }}</li>
                <li><b>Entrega:</b> {{ Auth::user()->name }}</li>
                <li><b>Fecha:</b> {{ $generatedAt->translatedFormat('d \d\e F \d\e\l Y') }}</li>
                <li><b>Codificación:</b> {{ $maleta->codigo ?? '—' }}</li>
            </ul>
        </li>

        <li>
            <p><b>DECLARACIÓN DE ENTREGA</b></p>
            <p>Por la presente se hace constancia de la entrega de maletas de herramientas para que cada trabajador
                pueda ejecutar sus labores. Se deberá cuidar minuciosamente cada herramienta y se deberá informar al
                área
                correspondiente de algún imprevisto (fracturado, reventador, deteriorado, etc). Esta lista deberá ser
                presentada en cada supervisión.</p>
        </li>

        <li>
            <p><b>INFORMACIÓN DE HERRAMIENTAS</b></p>
            <p>Total de herramientas: {{ $totalHerramientas }}</p>

            @if($herramientas->isEmpty())
                <p>No hay herramientas registradas.</p>
            @else
                <ul style="margin-left: -1rem; list-style-type: disc">
                    @foreach($herramientas as $herramienta)
                        <li>{{ $herramienta }}</li>
                    @endforeach
                </ul>
            @endif
        </li>
    </ol>

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
            <td style="padding-top: 5px">
                <p>FIRMA DEL COLABORADOR</p>
                <p style="margin: 0;"><b>Nombres:</b> {{ $maleta->propietario->name ?? '' }}</p>
                <p style="margin: 0;"><b>DNI:</b> {{ $maleta->propietario->dni ?? '' }}</p>
            </td>
            <td></td>
            <td style="padding-top: 5px;">
                <p>FIRMA DEL RESPONSABLE</p>
                <p style="margin: 0;"><b>Nombres:</b> {{ Auth::user()->name ?? '' }}</p>
                <p style="margin: 0;"><b>DNI:</b> {{ Auth::user()->dni ?? '' }}</p>
            </td>
            <td></td>
        </tr>
    </table>
</x-pdf-layout>