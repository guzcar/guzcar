<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use NumberToWords\NumberToWords;

class CotizacionPdfController extends Controller
{
    public function show(Cotizacion $cotizacion)
    {
        // Obtener cliente: primero el de la cotización, sino el primer cliente del vehículo
        $cliente = $cotizacion->cliente;
        if (!$cliente && $cotizacion->vehiculo) {
            $cliente = $cotizacion->vehiculo->clientes->first();
        }

        // Calcular totales
        $subtotal_servicios = $cotizacion->subtotal_servicios;
        $subtotal_articulos = $cotizacion->subtotal_articulos;
        $subtotal = $cotizacion->subtotal;
        $igv_calculado = $cotizacion->igv_calculado;
        $total = $cotizacion->total;

        // Generar palabras para el monto
        $numberToWords = new NumberToWords();
        $numberTransformer = $numberToWords->getNumberTransformer('es');
        $entero = floor($total);
        $decimal = round(($total - $entero) * 100);
        $palabras = strtoupper($numberTransformer->toWords($entero)) . " CON $decimal/100 SOLES";

        $pdf = Pdf::loadView('pdf.cotizacion', [
            'cotizacion' => $cotizacion,
            'cliente' => $cliente,
            'subtotal_servicios' => $subtotal_servicios,
            'subtotal_articulos' => $subtotal_articulos,
            'subtotal' => $subtotal,
            'igv_calculado' => $igv_calculado,
            'total' => $total,
            'palabras' => $palabras,
        ]);

        return $pdf->stream("cotizacion-{$cotizacion->id}.pdf");
    }
}