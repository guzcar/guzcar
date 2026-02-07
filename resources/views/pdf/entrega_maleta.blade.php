<x-pdf-layout title="Acta de Entrega {{ $entrega->maleta->codigo }}" code="{{ $entrega->maleta->codigo }}"
    tipoReporte="ACTA DE ENTREGA" footer="AUTOMOTORES GUZCAR S.A.C.">

    <h1>ENTREGA DE MALETA DE HERRAMIENTAS - PERSONAL</h1>

    {{-- Texto introductorio fuera de la lista numerada para que encabece el documento --}}
    <div style="font-size: 13px; text-align: justify; margin-bottom: 15px; text-transform: none;">
        Yo, <b>{{ $entrega->propietario->name ?? '____________________________________________________' }}</b>,
        identificado con DNI N.° <b>{{ $entrega->propietario->dni ?? '__________________________' }}</b>,
        a la fecha de: <b>{{ $generatedAt->format('d/m/Y') }}</b>,
        colaborador del taller <b>AUTOMOTORES GUZCAR S.A.C.</b>,
        Con RUC: <b>20600613716</b> declaro lo siguiente:
    </div>

    {{-- Usamos type="I" para números romanos (I, II, IV) --}}
    <ol type="I" style="text-transform: none; font-size: 13px; margin-left: -1rem; font-weight: bold;">

        <li style="margin-bottom: 10px;">
            <p>DECLARACIÓN DE RESPONSABILIDAD</p>
            {{-- Regresamos a font-weight normal para el contenido --}}
            <ol style="margin-left: -1rem; font-weight: normal; list-style-type: decimal;">
                <li>He recibido una maleta de herramientas debidamente inventariada.</li>
                <li>Me comprometo a usar las herramientas únicamente para labores propias del taller.</li>
                <li>Me obligo a cuidar, limpiar y conservar las herramientas en buen estado.</li>
                <li>Informaré de manera inmediata cualquier daño, pérdida o desperfecto.</li>
                <li>Asumiré la reposición de las herramientas perdidas o dañadas por descuido, negligencia o mal uso,
                    conforme a la evaluación del responsable del taller.</li>
            </ol>
        </li>

        <li style="margin-bottom: 10px;">
            <p>INVENTARIO DE HERRAMIENTAS ASIGNADAS</p>
            <p style="font-weight: normal; margin-top: 0;">Total de herramientas: {{ $totalHerramientas }}</p>

            @if($herramientas->isEmpty())
                <p style="font-weight: normal;">No hay herramientas registradas.</p>
            @else
                <ol style="margin-left: -1rem; font-weight: normal;">
                    @foreach($herramientas as $herramienta)
                        <li>{{ $herramienta }}</li>
                    @endforeach
                </ol>
            @endif
        </li>

        {{-- Saltamos al IV manualmente en el valor del LI para respetar tu formato --}}
        <li value="4">
            <p>CONFORMIDAD</p>
            <div style="font-weight: normal;">
                <p>Declaro haber recibido la maleta de herramientas descrita en el presente inventario, completa y en el
                    estado indicado.</p>
                <p>Fecha: {{ now()->format('d') }} / {{ now()->format('m') }} / {{ now()->format('Y') }}</p>
            </div>
        </li>

    </ol>

    {{-- Tu tabla de firmas original intacta, solo variables actualizadas --}}
    <table style="border-collapse: collapse; width: 100%; font-size: 13px;">
        <tr>
            <td style="width: 4%;"></td>

            <td
                style="width: 36%; border-bottom: 2px solid black; text-align: center; vertical-align: bottom; height: 100px;">
                @if(!empty($entrega->firma_propietario))
                    {{-- DomPDF renderiza Base64 directamente en el src --}}
                    <img src="{{ $entrega->firma_propietario }}" alt="Firma Propietario"
                        style="max-height: 90px; max-width: 100%; display: block; margin: 0 auto;">
                @else
                @endif
            </td>

            <td style="width: 20%;"></td>

            <td
                style="width: 36%; border-bottom: 2px solid black; text-align: center; vertical-align: bottom; height: 100px;">
                @if(!empty($entrega->firma_responsable))
                    <img src="{{ $entrega->firma_responsable }}" alt="Firma Responsable"
                        style="max-height: 90px; max-width: 100%; display: block; margin: 0 auto;">
                @else
                @endif
            </td>

            <td style="width: 4%;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-top: 5px">
                <p>FIRMA Y HUELLA DEL TRABAJADOR</p>
                <p style="margin: 0;"><b>Nombres:</b> {{ $entrega->propietario->name ?? '' }}</p>
                <p style="margin: 0;"><b>DNI:</b> {{ $entrega->propietario->dni ?? '' }}</p>
            </td>
            <td></td>
            <td style="padding-top: 5px;">
                <p>FIRMA DEL RESPONSABLE DEL TALLER</p>
                <p style="margin: 0;"><b>Nombres:</b> {{ $entrega->responsable->name ?? '' }}</p>
                <p style="margin: 0;"><b>DNI:</b> {{ $entrega->responsable->dni ?? '' }}</p>
            </td>
            <td></td>
        </tr>
    </table>
</x-pdf-layout>