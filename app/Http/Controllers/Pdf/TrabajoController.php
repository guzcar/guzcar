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

    /**
     * Mezcla, ordena y AGRUPA artículos repetidos.
     */
    private function mergeAndSortItems($articulos, $otros)
    {
        $collection = collect();

        // 1. Preparar Artículos
        foreach ($articulos as $art) {
            if (!$art->presupuesto)
                continue;

            // Clonamos el objeto para no afectar los datos originales en memoria si hay referencias cruzadas
            $item = clone $art;

            $item->tipo_item = 'articulo';
            $item->sort_index = $item->orden_combinado ?? 999999;
            // Clave única para identificar duplicados: ID + Precio (Para no mezclar precios distintos)
            $item->unique_key = 'art_' . $item->articulo_id . '_' . (float) $item->precio;

            $collection->push($item);
        }

        // 2. Preparar Otros
        foreach ($otros as $otro) {
            if (!$otro->presupuesto)
                continue;

            $item = clone $otro;

            $item->tipo_item = 'otro';
            $item->sort_index = $item->orden_combinado ?? 999999;
            // Los "otros" usualmente no se agrupan a menos que sean idénticos, 
            // pero mejor dejarlos separados o usar un ID único si quisieras.
            // Usamos el ID único de la fila para que nunca se agrupen entre sí.
            $item->unique_key = 'otro_' . $item->id;

            $collection->push($item);
        }

        // 3. Ordenar primero (para respetar "toma en cuenta el que aparece primero")
        $sorted = $collection->sortBy('sort_index')->values();

        // 4. Lógica de Agrupación (Merge)
        $finalCollection = collect();
        $seenItems = []; // Array auxiliar para rastrear lo que ya agregamos

        foreach ($sorted as $item) {
            // Si es 'otro', pasa directo (o aplica lógica si quieres agrupar otros idénticos)
            if ($item->tipo_item === 'otro') {
                $finalCollection->push($item);
                continue;
            }

            // Es un artículo: verificamos si ya existe en nuestro registro
            if (isset($seenItems[$item->unique_key])) {
                // YA EXISTE: Recuperamos la referencia del primer item encontrado
                $originalItem = $seenItems[$item->unique_key];

                // Sumamos la cantidad del item actual al original
                $originalItem->cantidad += $item->cantidad;

                // NO agregamos este item actual a $finalCollection, efectivamente "borrándolo"
            } else {
                // ES NUEVO: Lo guardamos en el registro y en la colección final
                $seenItems[$item->unique_key] = $item;
                $finalCollection->push($item);
            }
        }

        return $finalCollection;
    }

    private function calcularTiempo($ingreso, $salida)
    {
        if (empty($salida))
            return "EN TALLER";

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