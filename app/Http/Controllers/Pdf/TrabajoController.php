<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use DateTime;
use Illuminate\Support\Facades\App;

class TrabajoController extends Controller
{
    /**
     * Genera el PDF del presupuesto (Interno/Taller).
     */
    public function presupuesto($id)
    {
        $trabajo = Trabajo::with([
            'trabajoArticulos' => function ($query) {
                $query->where('presupuesto', true);
            },
            'trabajoArticulos.articulo.categoria',
            'trabajoArticulos.articulo.subCategoria',
            'trabajoArticulos.articulo.marca',
            'trabajoArticulos.articulo.unidad',
            'trabajoArticulos.articulo.presentacion',
            
            'servicios' => function ($query) {
                $query->where('presupuesto', true)->orderBy('sort');
            },
            'otros' => function ($query) {
                $query->where('presupuesto', true);
            },
            'descuentos',
            'cliente',
            'vehiculo.clientes',
            'vehiculo.tipoVehiculo',
            'vehiculo.marca',
            'vehiculo.modelo',
            'conductor'
        ])->find($id);

        // --- LÓGICA DE FUSIÓN Y ORDENAMIENTO ---
        $itemsUnificados = $this->mergeAndSortItems($trabajo->trabajoArticulos, $trabajo->otros);

        $clientePrincipal = $trabajo->cliente ?? $trabajo->vehiculo->clientes->first();
        $vehiculo = $trabajo->vehiculo;
        $tiempo = $this->calcularTiempo($trabajo->fecha_ingreso, $trabajo->fecha_salida);

        // --- CÁLCULOS ---
        $subtotal_servicios = $trabajo->servicios->sum(fn($s) => $s->cantidad * $s->precio);
        $subtotal_articulos = $trabajo->trabajoArticulos->where('presupuesto', true)->sum(fn($a) => $a->cantidad * $a->precio);
        $subtotal_trabajo_otros = $trabajo->otros->where('presupuesto', true)->sum(fn($o) => $o->cantidad * $o->precio);

        $total = $subtotal_articulos + $subtotal_servicios + $subtotal_trabajo_otros;

        // Definimos la variable explícitamente para compact()
        $descuentos = $trabajo->descuentos;
        $total_descuentos = 0;
        
        if ($descuentos->isNotEmpty()) {
            foreach ($descuentos as $descuento) {
                $monto_descuento = $total * ($descuento->descuento / 100);
                $total_descuentos += $monto_descuento;
            }
        }
        
        $total_con_descuentos = $total - $total_descuentos;

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.presupuesto', compact(
            'trabajo',
            'vehiculo',
            'tiempo',
            'subtotal_servicios',
            'subtotal_articulos',
            'subtotal_trabajo_otros',
            'itemsUnificados', 
            'total',
            'descuentos', // Ahora sí existe esta variable
            'total_descuentos',
            'total_con_descuentos',
            'clientePrincipal'
        ))->setPaper('A4', 'portrait');

        $codenow = now()->format('ymdhis');
        $fileName = "Presupuesto {$trabajo->codigo} - {$codenow}.pdf";

        return $pdf->stream($fileName);
    }

    /**
     * Genera el PDF de Presupuesto solo de Repuestos y Otros.
     */
    public function presupuestoArticulosRepuestosOtros($id)
    {
        $trabajo = Trabajo::with([
            'trabajoArticulos' => fn($q) => $q->where('presupuesto', true),
            'trabajoArticulos.articulo.categoria',
            'trabajoArticulos.articulo.subCategoria',
            'trabajoArticulos.articulo.marca',
            'trabajoArticulos.articulo.unidad',
            'trabajoArticulos.articulo.presentacion',
            
            'otros' => fn($q) => $q->where('presupuesto', true),
            
            'descuentos',
            'cliente',
            'vehiculo.clientes',
        ])->find($id);

        $itemsUnificados = $this->mergeAndSortItems($trabajo->trabajoArticulos, $trabajo->otros);

        $clientePrincipal = $trabajo->cliente ?? $trabajo->vehiculo->clientes->first();
        $vehiculo = $trabajo->vehiculo;
        $tiempo = $this->calcularTiempo($trabajo->fecha_ingreso, $trabajo->fecha_salida);

        // Cálculos
        $subtotal_articulos = $trabajo->trabajoArticulos->where('presupuesto', true)->sum(fn($a) => $a->cantidad * $a->precio);
        $subtotal_trabajo_otros = $trabajo->otros->where('presupuesto', true)->sum(fn($o) => $o->cantidad * $o->precio);
        
        $total = $subtotal_articulos + $subtotal_trabajo_otros;

        // CORRECCIÓN: Definir variable $descuentos
        $descuentos = $trabajo->descuentos;
        $total_descuentos = 0;

        if ($descuentos->isNotEmpty()) {
            foreach ($descuentos as $descuento) {
                $total_descuentos += $total * ($descuento->descuento / 100);
            }
        }
        $total_con_descuentos = $total - $total_descuentos;

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.presupuesto-articulos-repuestos-otros', compact(
            'trabajo',
            'vehiculo',
            'tiempo',
            'subtotal_articulos',
            'subtotal_trabajo_otros',
            'itemsUnificados', 
            'total',
            'total_descuentos',
            'total_con_descuentos',
            'clientePrincipal',
            'descuentos' // Esta variable causaba el error si no se definía arriba
        ))->setPaper('A4', 'portrait');

        $codenow = now()->format('ymdhis');
        $fileName = "Presupuesto Repuestos {$trabajo->codigo} - {$codenow}.pdf";

        return $pdf->stream($fileName);
    }

    /**
     * Genera la Proforma (Generalmente para cliente final).
     */
    public function proforma($id)
    {
        $trabajo = Trabajo::with([
            'trabajoArticulos' => fn($q) => $q->where('presupuesto', true),
            'trabajoArticulos.articulo.categoria',
            'trabajoArticulos.articulo.subCategoria',
            'trabajoArticulos.articulo.marca',
            'trabajoArticulos.articulo.unidad',
            'trabajoArticulos.articulo.presentacion',
            'servicios' => fn($q) => $q->where('presupuesto', true)->orderBy('sort'),
            'otros' => fn($q) => $q->where('presupuesto', true),
            'cliente',
            'vehiculo.clientes',
        ])->find($id);

        $itemsUnificados = $this->mergeAndSortItems($trabajo->trabajoArticulos, $trabajo->otros);

        $clientePrincipal = $trabajo->cliente ?? $trabajo->vehiculo->clientes->first();
        $vehiculo = $trabajo->vehiculo;
        $tiempo = $this->calcularTiempo($trabajo->fecha_ingreso, $trabajo->fecha_salida);

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.proforma', compact(
            'trabajo',
            'vehiculo',
            'tiempo',
            'itemsUnificados',
            'clientePrincipal'
        ))->setPaper('A4', 'portrait');

        $codenow = now()->format('ymdhis');
        $fileName = "Proforma {$trabajo->codigo} - {$codenow}.pdf";

        return $pdf->stream($fileName);
    }

    /**
     * Presupuesto solo de servicios.
     */
    public function presupuestoServicios($id)
    {
        $trabajo = Trabajo::with([
            'servicios' => fn($q) => $q->where('presupuesto', true)->orderBy('sort'),
            'descuentos',
            'cliente',
            'vehiculo.clientes',
        ])->find($id);

        $clientePrincipal = $trabajo->cliente ?? $trabajo->vehiculo->clientes->first();
        $vehiculo = $trabajo->vehiculo;
        $tiempo = $this->calcularTiempo($trabajo->fecha_ingreso, $trabajo->fecha_salida);

        $subtotal_servicios = $trabajo->servicios->sum(fn($s) => $s->cantidad * $s->precio);
        $total = $subtotal_servicios;

        // CORRECCIÓN: Definir variable $descuentos
        $descuentos = $trabajo->descuentos;
        $total_descuentos = 0;

        if ($descuentos->isNotEmpty()) {
            foreach ($descuentos as $descuento) {
                $total_descuentos += $total * ($descuento->descuento / 100);
            }
        }
        $total_con_descuentos = $total - $total_descuentos;

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.presupuesto-servicios', compact(
            'trabajo',
            'vehiculo',
            'tiempo',
            'subtotal_servicios',
            'total',
            'descuentos', // Ahora sí existe
            'total_descuentos',
            'total_con_descuentos',
            'clientePrincipal'
        ))->setPaper('A4', 'portrait');

        $codenow = now()->format('ymdhis');
        $fileName = "Presupuesto Servicios {$trabajo->codigo} - {$codenow}.pdf";

        return $pdf->stream($fileName);
    }

    // --- MÉTODOS AUXILIARES PRIVADOS ---

    private function mergeAndSortItems($articulos, $otros)
    {
        $collection = collect();

        foreach ($articulos as $art) {
            if (!$art->presupuesto) continue;
            $art->tipo_item = 'articulo';
            $art->sort_index = $art->orden_combinado ?? 999999; 
            $collection->push($art);
        }

        foreach ($otros as $otro) {
            if (!$otro->presupuesto) continue;
            $otro->tipo_item = 'otro';
            $otro->sort_index = $otro->orden_combinado ?? 999999;
            $collection->push($otro);
        }

        return $collection->sortBy('sort_index')->values();
    }

    private function calcularTiempo($ingreso, $salida)
    {
        if (empty($salida)) return "EN TALLER";
        
        $fecha_ingreso = new DateTime($ingreso);
        $fecha_salida = new DateTime($salida);
        $diferencia = $fecha_ingreso->diff($fecha_salida);
        $dias = $diferencia->days ?: 1;

        return $dias == 1 ? "{$dias} DÍA" : "{$dias} DÍAS";
    }

    public function evidencia($id)
    {
        $trabajo = Trabajo::find($id);
        $evidencias = $trabajo->evidencias()->where('mostrar', true)->orderBy('sort')->get();

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.evidencia', compact('trabajo', 'evidencias'))->setPaper('A4', 'portrait');
        return $pdf->stream('Evidencias ' . $trabajo->codigo . '.pdf');
    }

    public function informe($id)
    {
        $trabajo = Trabajo::with(['vehiculo.clientes', 'vehiculo.tipoVehiculo', 'vehiculo.marca', 'informes'])->find($id);
        
        $titulo = "# " . $trabajo->vehiculo->placa . " " .
            ($trabajo->vehiculo->tipoVehiculo->nombre ?? '') . " " .
            ($trabajo->vehiculo->marca->nombre ?? '') . " " .
            ($trabajo->vehiculo->modelo->nombre ?? '');

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf.informe', [
            'trabajo' => $trabajo,
            'informes' => $trabajo->informes,
            'titulo' => $titulo
        ])->setPaper('A4', 'portrait');

        return $pdf->stream("Informe {$trabajo->codigo}.pdf");
    }
}