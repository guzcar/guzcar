<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\ContenidoInforme;
use App\Models\Maleta;
use App\Models\MaletaDetalle;
use Barryvdh\DomPDF\Facade\Pdf;

class MaletaController extends Controller
{
    public function show(Maleta $maleta)
    {
        $maleta->load(['propietario', 'detalles.herramienta']);

        $contenidoInforme = ContenidoInforme::find(1)?->contenido;

        // Obtener todas las herramientas de la maleta y ordenar alfabéticamente
        $herramientas = $maleta->detalles->map(function ($detalle) {
            return $detalle->herramienta->nombre;
        })->filter()->sort()->values();

        // Renderizar el PDF
        $pdf = Pdf::loadView('pdf.maleta', [
            'maleta' => $maleta,
            'generatedAt' => now(),
            'herramientas' => $herramientas,
            'contenidoInforme' => $contenidoInforme,
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("maleta-{$maleta->codigo}.pdf");
    }

    public function detallesSeleccionados(Maleta $maleta, string $detalles)
    {
        $detalleIds = explode(',', $detalles);
        
        // Cargar solo los detalles seleccionados
        $detallesSeleccionados = MaletaDetalle::with('herramienta')
            ->whereIn('id', $detalleIds)
            ->where('maleta_id', $maleta->id)
            ->get();
            
        $maleta->load('propietario');
        
        // Obtener las herramientas de los detalles seleccionados y ordenar alfabéticamente
        $herramientas = $detallesSeleccionados->map(function ($detalle) {
            return $detalle->herramienta->nombre;
        })->filter()->sort()->values();

        // Renderizar el PDF
        $pdf = Pdf::loadView('pdf.maleta-detalle', [
            'maleta' => $maleta,
            'generatedAt' => now(),
            'herramientas' => $herramientas,
            'totalHerramientas' => $detallesSeleccionados->count(),
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("maleta-{$maleta->codigo}-seleccionadas.pdf");
    }
}