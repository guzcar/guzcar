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
    public function presupuesto($id)
    {
        $trabajo = Trabajo::with([
            'trabajoArticulos' => function ($query) {
                $query->where('presupuesto', true)
                    ->orderBy('sort'); // Ordenar por sort
            },
            'trabajoArticulos.articulo.categoria',
            'trabajoArticulos.articulo.subCategoria',
            'trabajoArticulos.articulo.marca',
            'trabajoArticulos.articulo.unidad',
            'trabajoArticulos.articulo.presentacion',
            'servicios' => function ($query) {
                $query->orderBy('sort'); // Ordenar servicios por sort
            },
            'otros' => function ($query) {
                $query->orderBy('sort'); // Ordenar otros por sort
            },
            'descuentos',
            'cliente',
            'vehiculo.clientes',
        ])->find($id);

        // Resto del código permanece igual...
        $clientePrincipal = $trabajo->cliente ?? $trabajo->vehiculo->clientes->first();

        $vehiculo = $trabajo->vehiculo;

        $fecha_ingreso = $trabajo->fecha_ingreso;
        $fecha_salida = $trabajo->fecha_salida;

        if (empty($fecha_salida)) {
            $tiempo = "EN TALLER";
        } else {
            $fecha_ingreso = new DateTime($fecha_ingreso);
            $fecha_salida = new DateTime($fecha_salida);
            $diferencia = $fecha_ingreso->diff($fecha_salida);
            $dias = $diferencia->days ?: 1;

            $tiempo = $dias == 1 ? "{$dias} DÍA" : "{$dias} DÍAS";
        }

        // Subtotal de servicios (ya vienen ordenados por sort)
        $subtotal_servicios = $trabajo->servicios->sum(function ($trabajoServicio) {
            return $trabajoServicio->cantidad * $trabajoServicio->precio;
        });

        // Agrupar artículos por ID y precio, sumando sus cantidades (manteniendo el orden)
        $articulosAgrupados = $trabajo->trabajoArticulos->groupBy(function ($articulo) {
            return $articulo->articulo_id . '-' . $articulo->precio;
        })->map(function ($grupo) {
            return [
                'articulo' => $grupo->first()->articulo,
                'precio' => $grupo->first()->precio,
                'cantidad' => $grupo->sum('cantidad'),
            ];
        });

        // Subtotal de artículos
        $subtotal_articulos = $articulosAgrupados->sum(function ($articulo) {
            return $articulo['cantidad'] * $articulo['precio'];
        });

        // Subtotal de trabajo_otros (ya vienen ordenados por sort)
        $subtotal_trabajo_otros = $trabajo->otros->sum(function ($trabajoOtro) {
            return $trabajoOtro->cantidad * $trabajoOtro->precio;
        });

        $total = $subtotal_articulos + $subtotal_servicios + $subtotal_trabajo_otros;

        $descuentos = $trabajo->descuentos;
        $total_descuentos = 0;
        $total_con_descuentos = $total;

        if ($descuentos->isNotEmpty()) {
            foreach ($descuentos as $descuento) {
                $monto_descuento = $total * ($descuento->descuento / 100);
                $total_descuentos += $monto_descuento;
            }
            $total_con_descuentos = $total - $total_descuentos;
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.presupuesto', compact(
            'trabajo',
            'vehiculo',
            'tiempo',
            'subtotal_servicios',
            'subtotal_articulos',
            'articulosAgrupados',
            'subtotal_trabajo_otros',
            'total',
            'descuentos',
            'total_descuentos',
            'total_con_descuentos',
            'clientePrincipal'
        ))->setPaper('A4', 'portrait');

        $codenow = now()->format('ymdhis');
        $fileName = "Presupuesto {$trabajo->codigo} - {$codenow}.pdf";

        return $pdf->stream($fileName);
    }

    public function proforma($id)
    {
        $trabajo = Trabajo::with([
            'trabajoArticulos' => function ($query) {
                $query->where('presupuesto', true)
                    ->orderBy('sort'); // Ordenar por sort
            },
            'trabajoArticulos.articulo.categoria',
            'trabajoArticulos.articulo.subCategoria',
            'trabajoArticulos.articulo.marca',
            'trabajoArticulos.articulo.unidad',
            'trabajoArticulos.articulo.presentacion',
            'otros' => function ($query) {
                $query->orderBy('sort'); // Ordenar otros por sort
            },
            'cliente',
            'vehiculo.clientes',
        ])->find($id);

        // Resto del código permanece igual...
        $clientePrincipal = $trabajo->cliente ?? $trabajo->vehiculo->clientes->first();
        $vehiculo = $trabajo->vehiculo;

        $fecha_ingreso = $trabajo->fecha_ingreso;
        $fecha_salida = $trabajo->fecha_salida;

        if (empty($fecha_salida)) {
            $tiempo = "EN TALLER";
        } else {
            $fecha_ingreso = new DateTime($fecha_ingreso);
            $fecha_salida = new DateTime($fecha_salida);
            $diferencia = $fecha_ingreso->diff($fecha_salida);
            $dias = $diferencia->days ?: 1;
            $tiempo = $dias == 1 ? "{$dias} DÍA" : "{$dias} DÍAS";
        }

        $articulosAgrupados = $trabajo->trabajoArticulos->groupBy('articulo_id')->map(function ($grupo) {
            return [
                'articulo' => $grupo->first()->articulo,
                'cantidad' => $grupo->sum('cantidad'),
            ];
        });

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.proforma', compact(
            'trabajo',
            'vehiculo',
            'tiempo',
            'articulosAgrupados',
            'clientePrincipal'
        ))->setPaper('A4', 'portrait');

        $codenow = now()->format('ymdhis');
        $fileName = "Proforma {$trabajo->codigo} - {$codenow}.pdf";

        return $pdf->stream($fileName);
    }

    public function evidencia($id)
    {
        $trabajo = Trabajo::find($id);
        $evidencias = $trabajo->evidencias()
            ->where('mostrar', true)
            ->orderBy('sort')
            ->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.evidencia', compact('trabajo', 'evidencias'))->setPaper('A4', 'portrait');
        $fileName = 'Evidencias ' . $trabajo->codigo . '.pdf';
        return $pdf->stream($fileName);
    }

    public function informe($id)
    {
        $trabajo = Trabajo::with([
            'vehiculo.clientes',
            'vehiculo.tipoVehiculo',
            'vehiculo.marca',
            'informes'
        ])->find($id);

        // Construir el título con los datos del vehículo
        $titulo = "# " . $trabajo->vehiculo->placa . " " .
            ($trabajo->vehiculo->tipoVehiculo->nombre ?? '') . " " .
            ($trabajo->vehiculo->marca->nombre ?? '') . " " .
            ($trabajo->vehiculo->modelo->nombre ?? '');

        $pdf = App::make('dompdf.wrapper');

        // Pasar solo los datos necesarios a la vista
        $pdf->loadView('pdf.informe', [
            'trabajo' => $trabajo,
            'informes' => $trabajo->informes,
            'titulo' => $titulo
        ])->setPaper('A4', 'portrait');

        $fileName = "Informe {$trabajo->codigo}.pdf";

        return $pdf->stream($fileName);
    }
}
