<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\TrabajoDescripcionTecnico;
use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Articulo;
use App\Models\Servicio;

class VehiculoController extends Controller
{
    public function consultaVehicular()
    {
        return view('vehiculos.consulta_vehicular');
    }

    public function buscarVehiculo(Request $request)
    {
        $placa = $request->query('placa');

        // Limpiamos la placa: eliminamos guiones, espacios y pasamos a mayúsculas
        $placaLimpia = strtoupper(str_replace(['-', ' '], '', $placa));

        // Buscamos ignorando guiones y mayúsculas/minúsculas en la BD
        $vehiculo = Vehiculo::whereRaw("REPLACE(UPPER(placa), '-', '') = ?", [$placaLimpia])
            ->first();

        $trabajos = collect();
        if ($vehiculo) {
            // Eager loading con TODAS las relaciones necesarias para armar el nombre del artículo
            $trabajos = $vehiculo->trabajos()
                ->with([
                    'usuarios',
                    'taller',
                    'otros',
                    'articulos.categoria',
                    'articulos.marca',
                    'articulos.subCategoria.categoria',
                    'articulos.presentacion',
                    'articulos.unidad'
                ])
                ->withCount(['evidencias', 'descripcionTecnicos'])
                ->orderByDesc('fecha_ingreso')
                ->paginate(5)
                ->appends(['placa' => $placa]);

            // Transformamos la colección para adjuntar el resumen exacto de artículos (tu lógica)
            $trabajos->getCollection()->transform(function ($trabajo) {
                $trabajo->articulos_resumen = collect($trabajo->articulos ?? [])
                    ->groupBy('id')
                    ->map(function ($items) {
                        $a = $items->first();

                        // Suma de cantidades desde el pivot
                        $cantidad = $items->sum(function ($it) {
                            $raw = $it->pivot->cantidad ?? 0;
                            return is_numeric($raw) ? (float) $raw : 0.0;
                        });

                        // Replicando la lógica de tu ViewTrabajo.php
                        $categoriaNombre = optional($a->categoria)->nombre
                            ?? optional(optional($a->subCategoria)->categoria)->nombre;

                        $parts = array_values(array_filter([
                            $categoriaNombre,
                            optional($a->marca)->nombre,
                            optional($a->subCategoria)->nombre,
                            $a->especificacion,
                            optional($a->presentacion)->nombre,
                            $a->medida,
                            optional($a->unidad)->nombre,
                            $a->color,
                        ], fn($v) => is_string($v) && trim($v) !== ''));

                        $nombre = trim(implode(' ', $parts));
                        if ($nombre === '' && isset($a->nombre) && trim($a->nombre) !== '') {
                            $nombre = trim($a->nombre);
                        }
                        if ($nombre === '') {
                            $nombre = 'Artículo';
                        }

                        return (object) [
                            'nombre' => $nombre,
                            'cantidad' => $cantidad,
                        ];
                    })->values();

                return $trabajo;
            });
        }

        return view('vehiculos.consulta_vehicular', compact('placa', 'vehiculo', 'trabajos'));
    }

    public function articulosUtilizados($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        // Obtener artículos únicos directamente desde la tabla de artículos
        $articulos = Articulo::whereHas('trabajos', function ($query) use ($vehiculo) {
            $query->where('vehiculo_id', $vehiculo->id);
        })
            ->with('subCategoria.categoria')
            ->distinct()
            ->get();

        return view('vehiculos.articulos_utilizados', compact('articulos', 'vehiculo'));
    }

    public function serviciosEjecutados($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        // Obtener servicios únicos directamente desde la tabla de servicios
        $servicios = Servicio::whereHas('trabajos', function ($query) use ($vehiculo) {
            $query->where('vehiculo_id', $vehiculo->id);
        })
            ->distinct()
            ->get();

        return view('vehiculos.servicios_ejecutados', compact('servicios', 'vehiculo'));
    }

    public function evidenciasTrabajo($id)
    {
        $trabajo = Trabajo::with(['vehiculo', 'evidencias.user'])->findOrFail($id);

        // Obtenemos las evidencias paginadas
        $evidencias = $trabajo->evidencias()->orderBy('created_at', 'desc')->paginate(12);

        return view('vehiculos.evidencias', compact('trabajo', 'evidencias'));
    }

    public function detallesTrabajo($id)
    {
        $trabajo = Trabajo::with(['vehiculo.marca', 'vehiculo.modelo', 'vehiculo.tipoVehiculo'])->findOrFail($id);

        // Obtenemos los detalles de la misma forma que en el TrabajoDescripcionTecnicoController
        $detalles = TrabajoDescripcionTecnico::where('trabajo_id', $trabajo->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('vehiculos.detalles', compact('trabajo', 'detalles'));
    }
}
