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
        $trabajo = Trabajo::with([
            'trabajoArticulos.articulo.subCategoria.categoria', // Cargar relaciones anidadas
            'trabajoArticulos.articulo' // Cargar la relación de artículo
        ])->find($id);

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

        // Subtotal de servicios
        $subtotal_servicios = $trabajo->servicios->sum(function ($trabajoServicio) {
            return $trabajoServicio->cantidad * $trabajoServicio->precio;
        });

        // Agrupar artículos por ID y precio, sumando sus cantidades
        $articulosAgrupados = $trabajo->trabajoArticulos->groupBy(function ($articulo) {
            return $articulo->articulo_id . '-' . $articulo->precio; // Agrupa por ID y precio
        })->map(function ($grupo) {
            return [
                'articulo' => $grupo->first()->articulo, // Tomamos el primer artículo del grupo
                'precio' => $grupo->first()->precio, // Tomamos el precio del primer artículo
                'cantidad' => $grupo->sum('cantidad'), // Sumamos las cantidades
            ];
        });

        // Subtotal de artículos
        $subtotal_articulos = $articulosAgrupados->sum(function ($articulo) {
            return $articulo['cantidad'] * $articulo['precio'];
        });

        $total = $subtotal_articulos + $subtotal_servicios;

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.trabajo', compact(
            'trabajo',
            'vehiculo',
            'tiempo',
            'subtotal_servicios',
            'subtotal_articulos',
            'articulosAgrupados',
            'total', // Pasamos los artículos agrupados a la vista
        ))->setPaper('A4', 'portrait');

        $fileName = 'Proforma ' . $trabajo->codigo . ' - ' . now()->format('ymdhis') . '.pdf';
        return $pdf->stream($fileName);
    }

    public function evidencia($id)
    {
        $trabajo = Trabajo::find($id);
        $evidencias = $trabajo->evidencias;

        // return view('pdf.evidencia', compact('trabajo', 'evidencias'));

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.evidencia', compact('trabajo', 'evidencias'))->setPaper('A4', 'portrait');
        $fileName = 'Evidencias ' . $trabajo->codigo . '.pdf';
        return $pdf->stream($fileName);
    }
}
