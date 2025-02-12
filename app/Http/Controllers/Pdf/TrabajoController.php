<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use DateTime;
use Illuminate\Support\Facades\App;

class TrabajoController extends Controller
{
    /**
     * Genera el PDF de la proforma.
     * 
     * @param mixed $id
     */
    public function report($id)
    {
        $trabajo = Trabajo::find($id);

        $vehiculo = $trabajo->vehiculo;

        $fecha_ingreso = $trabajo->fecha_ingreso;
        $fecha_salida = $trabajo->fecha_salida;

        if (empty($fecha_salida)) {
            $tiempo = "EN TALLER";
        } else {
            $fecha_ingreso = new DateTime($fecha_ingreso);
            $fecha_salida = new DateTime($fecha_salida);
            $diferencia = $fecha_ingreso->diff($fecha_salida);
            $tiempo = "{$diferencia->days} DÍAS";
        }

        $subtotal_servicios = $trabajo->servicios->sum(function ($trabajoServicio) {
            return $trabajoServicio->cantidad * $trabajoServicio->precio;
        });

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.trabajo', compact(
            'trabajo',
            'vehiculo',
            'tiempo',
            'subtotal_servicios'
        ))->setPaper('A4', 'portrait');
        $fileName = 'Proforma ' . $trabajo->vehiculo->placa . '.pdf';
        return $pdf->stream($fileName);
    }
}
