<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\ContenidoInforme;
use App\Models\EntregaMaleta;
use App\Models\EntregaMaletaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class EntregaMaletaController extends Controller
{
    // Opción 1: Imprimir TODO lo que contiene esa entrega
    public function show(EntregaMaleta $entrega)
    {
        // Cargamos relaciones. Notar que accedemos a 'maleta' para sacar el código
        $entrega->load(['propietario', 'responsable', 'maleta', 'detalles.herramienta']);

        $contenidoInforme = ContenidoInforme::find(1)?->contenido;

        // Mapeamos desde los detalles de la entrega, no de la maleta actual
        $herramientas = $entrega->detalles->map(function ($detalle) {
            return $detalle->herramienta->nombre;
        })->filter()->sort()->values();

        $pdf = Pdf::loadView('pdf.entrega_maleta', [
            'entrega' => $entrega,
            'generatedAt' => now(), // O puedes usar $entrega->fecha si prefieres la fecha del registro
            'herramientas' => $herramientas,
            'contenidoInforme' => $contenidoInforme,
            'totalHerramientas' => $entrega->detalles->count(),
            'soloSeleccionados' => false
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("acta-entrega-{$entrega->id}.pdf");
    }

    // Opción 2: Imprimir solo items seleccionados de esa entrega
    public function detallesSeleccionados(EntregaMaleta $entrega, string $detalles)
    {
        $detalleIds = explode(',', $detalles);
        
        // Buscamos en EntregaMaletaDetalle
        $detallesSeleccionados = EntregaMaletaDetalle::with('herramienta')
            ->whereIn('id', $detalleIds)
            ->where('entrega_maleta_id', $entrega->id)
            ->get();
            
        $entrega->load(['propietario', 'responsable', 'maleta']);
        
        $herramientas = $detallesSeleccionados->map(function ($detalle) {
            return $detalle->herramienta->nombre;
        })->filter()->sort()->values();

        $contenidoInforme = ContenidoInforme::find(1)?->contenido;

        $pdf = Pdf::loadView('pdf.entrega_maleta', [
            'entrega' => $entrega,
            'generatedAt' => now(),
            'herramientas' => $herramientas,
            'contenidoInforme' => $contenidoInforme,
            'totalHerramientas' => $detallesSeleccionados->count(),
            'soloSeleccionados' => true
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("acta-entrega-{$entrega->id}-parcial.pdf");
    }
}