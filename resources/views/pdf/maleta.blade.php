<x-pdf-layout title="Maleta {{ $maleta->codigo }}" code="{{ $maleta->codigo }}" tipoReporte="ACTA DE ENTRAGA" footer="AUTOMOTORES GUZCAR S.A.C.">

    <h1>ACTA DE ENTREGA - MALETA DE HERRAMIENTAS</h1>

    <ol style="text-transform: none">
        <li>
            <p><b>INFORMACIÓN GENERAL</b></p>
            <p>{{ $contenidoInforme }}</p>
        </li>

        <li>
            <p><b>INFORMACIÓN DE DATOS</b></p>
            <ul>
                <li><b>Nombres de quién recibió:</b> {{ $maleta->propietario->name ?? '—' }}</li>
                <li><b>Nombres de quién entrega:</b> {{ Auth::user()->name }}</li>
                <li><b>Fecha de entrega:</b> {{ $generatedAt->translatedFormat('d \d\e F \d\e\l Y') }}</li>
                <li><b>Codificación:</b> {{ $maleta->codigo ?? '—' }}</li>
            </ul>
        </li>

        <li>
            <p><b>INFORMACIÓN DE HERRAMIENTAS</b></p>
            <p>Total de herramientas: {{ $maleta->detalles->count() }}</p>

            @if($herramientasAgrupadas->isEmpty())
                <p>No hay herramientas registradas.</p>
            @else
                <ul>
                    @foreach($herramientasAgrupadas as $grupo)
                        <li>
                            <b>{{ $grupo['cantidad'] }} × {{ $grupo['prefijo'] }}{{ count($grupo['variantes']) > 0 && $grupo['variantes'][0] !== '' ? ':' : '' }}</b>
                            @if(count($grupo['variantes']) > 0 && $grupo['variantes'][0] !== '')
                                {{ implode(', ', array_map('strtoupper', $grupo['variantes'])) }}.
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    </ol>

    <table style="border-collapse: collapse; width: 100%;">
        <tr>
            <td style="width: 21%; padding-top: 50px;"></td>
            <td style="width: 31%; border-bottom: 2px solid black;"></td>
            <td style="width: 21%;"></td>
            <td style="width: 31%; border-bottom: 2px solid black;"></td>
            <td style="width: 21%;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="text-align: center; padding-top: 10px;">{{ $maleta->propietario->name ?? '—' }}</td>
            <td></td>
            <td style="text-align: center; padding-top: 10px;">{{ Auth::user()->name }}</td>
            <td></td>
        </tr>
    </table>
</x-pdf-layout>