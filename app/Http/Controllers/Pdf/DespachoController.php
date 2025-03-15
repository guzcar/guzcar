<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Despacho;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DespachoController extends Controller
{
    public function downloadPdf(Despacho $despacho)
    {
        $despacho->load(
            'trabajoArticulos.articulo.categoria',
            'trabajoArticulos.articulo.subCategoria',
            'trabajoArticulos.articulo.marca',
            'trabajoArticulos.articulo.unidad',
            'trabajoArticulos.articulo.presentacion'
        );

        $pdf = Pdf::loadView('pdf.despacho', compact('despacho'));

        $codenow = now()->format('ymdhis');

        return $pdf->stream("Despacho {$despacho->codigo} - {$codenow}.pdf");
    }
}
