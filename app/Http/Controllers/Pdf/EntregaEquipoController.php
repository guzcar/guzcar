<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\ContenidoInforme;
use App\Models\EntregaEquipo;
use App\Models\EntregaEquipoDetalle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class EntregaEquipoController extends Controller
{
    // Opción 1: Imprimir TODO lo que contiene esa entrega
    public function show(EntregaEquipo $entrega)
    {
        // Cargamos relaciones.
        $entrega->load(['propietario', 'responsable', 'equipo', 'detalles.implemento']);

        // Asumo que usas el mismo contenido legal (ID 1), si tienes otro para equipos, cambia el ID.
        $contenidoInforme = ContenidoInforme::find(1)?->contenido; 

        // Mapeamos desde los detalles de la entrega
        $implementos = $entrega->detalles->map(function ($detalle) {
            return $detalle->implemento->nombre;
        })->filter()->sort()->values();

        $pdf = Pdf::loadView('pdf.entrega_equipo', [
            'entrega' => $entrega,
            'generatedAt' => now(), 
            'implementos' => $implementos,
            'contenidoInforme' => $contenidoInforme,
            'totalImplementos' => $entrega->detalles->count(),
            'soloSeleccionados' => false
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("acta-entrega-equipo-{$entrega->id}.pdf");
    }

    // Opción 2: Imprimir solo items seleccionados de esa entrega
    public function detallesSeleccionados(EntregaEquipo $entrega, string $detalles)
    {
        $detalleIds = explode(',', $detalles);
        
        // Buscamos en EntregaEquipoDetalle
        $detallesSeleccionados = EntregaEquipoDetalle::with('implemento')
            ->whereIn('id', $detalleIds)
            ->where('entrega_equipo_id', $entrega->id)
            ->get();
            
        $entrega->load(['propietario', 'responsable', 'equipo']);
        
        $implementos = $detallesSeleccionados->map(function ($detalle) {
            return $detalle->implemento->nombre;
        })->filter()->sort()->values();

        $contenidoInforme = ContenidoInforme::find(1)?->contenido;

        $pdf = Pdf::loadView('pdf.entrega_equipo', [
            'entrega' => $entrega,
            'generatedAt' => now(),
            'implementos' => $implementos,
            'contenidoInforme' => $contenidoInforme,
            'totalImplementos' => $detallesSeleccionados->count(),
            'soloSeleccionados' => true
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("acta-entrega-equipo-{$entrega->id}-parcial.pdf");
    }
}