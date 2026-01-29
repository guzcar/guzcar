<x-pdf-layout title="Acta de Entrega {{ $entrega->equipo->codigo }}" code="{{ $entrega->equipo->codigo }}" tipoReporte="ACTA DE ENTREGA" footer="AUTOMOTORES GUZCAR S.A.C.">

    <h1>ENTREGA DE EQUIPO E IMPLEMENTOS - PERSONAL</h1>

    {{-- Texto introductorio --}}
    <div style="font-size: 13px; text-align: justify; margin-bottom: 15px; text-transform: none;">
        Yo, <b>{{ $entrega->propietario->name ?? '____________________________________________________' }}</b>, 
        identificado con DNI N.° <b>{{ $entrega->propietario->dni ?? '__________________________' }}</b>, 
        a la fecha de: <b>{{ $generatedAt->format('d/m/Y') }}</b>, 
        colaborador del taller <b>AUTOMOTORES GUZCAR S.A.C.</b>, 
        Con RUC: <b>20600613716</b> declaro lo siguiente:
    </div>

    <ol type="I" style="text-transform: none; font-size: 13px; margin-left: -1rem; font-weight: bold;">

        <li style="margin-bottom: 10px;">
            <p>DECLARACIÓN DE RESPONSABILIDAD</p>
            <ol style="margin-left: -1rem; font-weight: normal; list-style-type: decimal;">
                <li>He recibido un equipo con sus implementos debidamente inventariados.</li>
                <li>Me comprometo a usar los implementos únicamente para labores propias del taller.</li>
                <li>Me obligo a cuidar, limpiar y conservar los implementos en buen estado.</li>
                <li>Informaré de manera inmediata cualquier daño, pérdida o desperfecto.</li>
                <li>Asumiré la reposición de los implementos perdidos o dañados por descuido, negligencia o mal uso, conforme a la evaluación del responsable del taller.</li>
            </ol>
        </li>

        <li style="margin-bottom: 10px;">
            <p>INVENTARIO DE IMPLEMENTOS ASIGNADOS</p>
            <p style="font-weight: normal; margin-top: 0;">Total de implementos: {{ $totalImplementos }}</p>

            @if($implementos->isEmpty())
                <p style="font-weight: normal;">No hay implementos registrados.</p>
            @else
                <ol style="margin-left: -1rem; font-weight: normal;">
                    @foreach($implementos as $implemento) 
                        <li>{{ $implemento }}</li>
                    @endforeach
                </ol>
            @endif
        </li>

        <li value="4">
            <p>CONFORMIDAD</p>
            <div style="font-weight: normal;">
                <p>Declaro haber recibido el equipo e implementos descritos en el presente inventario, completos y en el estado indicado.</p>
                <p>Fecha: {{ now()->format('d') }} / {{ now()->format('m') }} / {{ now()->format('Y') }}</p>
            </div>
        </li>

    </ol>

    <table style="border-collapse: collapse; width: 100%; font-size: 13px;">
        <tr>
            <td style="width: 4%; padding-top: 90px;"></td>
            <td style="width: 36%; border-bottom: 2px solid black;"></td>
            <td style="width: 20%;"></td>
            <td style="width: 36%; border-bottom: 2px solid black;"></td>
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