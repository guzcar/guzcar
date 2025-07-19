<x-pdf-layout title="Informe {{ $trabajo->codigo }}" code="{{ $trabajo->codigo }}" tipoReporte="INFORME TÉCNICO">
    <div class="mb-4">
        <h2 class="bold text-center">{{ $titulo }}</h2>
        <h2 class="normal text-center">INFORME TÉCNICO</h2>
    </div>

    @foreach($informes as $informe)
        <div style="font-size: 13px; text-align: justify; text-justify: inter-word;">
            {!! $informe->contenido !!}
        </div>
    @endforeach
</x-pdf-layout>