<x-pdf-layout title="Evidencias {{ $trabajo->codigo }}" code="{{ $trabajo->codigo }}" tipoReporte="EVIDENCIAS">
    <div style="width: 100%; text-align: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0;">Evidencias fotográficas</h3>
    </div>
    
    <table style="width: 100%; border-collapse: collapse;">
        <tr style="page-break-inside: avoid;">
            @php $count = 0; @endphp
            @foreach ($evidencias as $evidencia)
                @if ($evidencia->tipo === 'imagen')
                    @if ($count % 3 === 0 && $count !== 0)
                        </tr>
                        <tr style="page-break-inside: avoid;">
                    @endif
                    
                    <td style="width: 33.33%; padding-bottom: 20px; text-align: center; vertical-align: top;">
                        <div style="display: inline-block; width: 95%;">
                            <img src="{{ $evidencia->thumbnail_base64 }}" 
                                style="max-width: 100%; max-height: 190px; width: auto; height: auto; display: block;">
                        </div>
                    </td>
                    @php $count++; @endphp
                @endif
            @endforeach
            
            @while($count % 3 !== 0)
                <td style="width: 33.33%;"></td>
                @php $count++; @endphp
            @endwhile
        </tr>
    </table>
</x-pdf-layout>