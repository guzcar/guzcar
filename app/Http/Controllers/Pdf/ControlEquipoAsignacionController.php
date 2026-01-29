<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\ControlEquipo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ControlEquipoAsignacionController extends Controller
{
    public function show(ControlEquipo $control)
    {
        // Cargar relaciones
        $control->load([
            'equipo.propietario',
            'responsable',
            'propietario',
            'detalles.implemento',
        ]);

        // Ordenamos los detalles por nombre de implemento (case-insensitive)
        $detalles = $control->detalles
            ->sortBy(fn($d) => mb_strtoupper($d->implemento->nombre ?? ''), SORT_NATURAL)
            ->values();

        $pdf = Pdf::loadView('pdf.hoja-asignacion-equipo', [
            'control' => $control,
            'equipo' => $control->equipo,
            'detalles' => $detalles,
            'generatedAt' => now(),
        ])->setPaper('A4', 'portrait');

        $equipoCodigo = $control->equipo?->codigo ?? 'SIN-CODIGO';
        $fecha = $control->fecha?->format('Ymd') ?? now()->format('Ymd');

        // Abrir inline en nueva pestaña
        return $pdf->stream("hoja-asignacion-equipo-{$equipoCodigo}-{$fecha}.pdf");
    }
}