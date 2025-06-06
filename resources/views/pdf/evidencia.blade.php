<x-pdf-layout title="Evidencias {{ $trabajo->codigo }}" code="{{ $trabajo->codigo }}" tipoReporte="EVIDENCIAS">
    <div style="width: 100%; text-align: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0;">Evidencias fotográficas</h3>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            @php $count = 0; @endphp
            @foreach ($evidencias as $evidencia)
                @if ($evidencia->tipo === 'imagen')
                    @if ($count % 3 === 0 && $count !== 0)
                        </tr>
                        <tr>
                    @endif
                    <td style="width: 33.33%; padding-bottom: 20px; text-align: center; vertical-align: top;">
                        <div style="display: inline-block; max-width: 100%; max-height: 190px; 
                                @if ($count % 3 === 1) margin: 0 10px; @else margin: 0; @endif">
                            <img src="{{ public_path('storage/' . str_replace('public/', '', $evidencia->evidencia_url)) }}"
                                style="max-width: 100%; max-height: 190px; width: auto; height: auto; display: block;">
                        </div>
                    </td>
                    @php $count++; @endphp
                @endif
            @endforeach
        </tr>
    </table>
</x-pdf-layout>