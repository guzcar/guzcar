<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function downloadPdf(Venta $venta)
    {
        $venta->load('ventaArticulos.articulo');

        $pdf = Pdf::loadView('pdf.venta', compact('venta'));

        $codenow = now()->format('ymdhis');

        return $pdf->stream("Venta {$venta->codigo} - {$codenow}.pdf");
    }
}
