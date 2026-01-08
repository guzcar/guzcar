<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use App\Models\TrabajoArticulo;
use App\Models\TrabajoOtro;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticuloController extends Controller
{
    /**
     * 1. Listar artículos y otros de un determinado vehículo (Trabajo)
     */
    public function itemsPorTrabajo(Trabajo $trabajo)
    {
        $userId = Auth::id();

        // Cargar artículos normales
        $articulos = TrabajoArticulo::where('trabajo_id', $trabajo->id)
            ->where('tecnico_id', $userId)
            ->with(['articulo.subCategoria.categoria', 'articulo.marca', 'articulo.unidad', 'articulo.presentacion'])
            ->get();

        // Cargar "otros"
        $otros = TrabajoOtro::where('trabajo_id', $trabajo->id)
            ->where('user_id', $userId)
            ->get();

        // Combinar y ordenar
        $todosLosItems = $this->combinarYOrdenar($articulos, $otros);

        // Formatear respuesta (incluyendo datos del vehículo)
        return response()->json([
            'vehiculo' => [
                'placa' => $trabajo->vehiculo->placa,
                'tipo' => $trabajo->vehiculo->tipoVehiculo->nombre ?? 'N/A',
                'marca' => $trabajo->vehiculo->marca->nombre ?? 'N/A',
                'modelo' => $trabajo->vehiculo->modelo->nombre ?? 'N/A',
                'color' => $trabajo->vehiculo->color,
                'fecha_ingreso' => $trabajo->fecha_ingreso,
            ],
            'items' => $todosLosItems->map(function ($item) {
                return $this->formatearItem($item);
            })->values() // values() resetea los índices del array
        ]);
    }

    /**
     * 2. Confirmar todos los artículos y otros de un trabajo
     */
    public function confirmarTodosPorTrabajo(Trabajo $trabajo)
    {
        $userId = Auth::id();

        DB::transaction(function () use ($trabajo, $userId) {
            // Confirmar Artículos
            $trabajo->trabajoArticulos()
                ->where('tecnico_id', $userId)
                ->where('confirmado', false) // Solo actualizar los pendientes
                ->update(['confirmado' => true]);

            // Confirmar Otros
            $trabajo->otros()
                ->where('user_id', $userId)
                ->where('confirmado', false)
                ->update(['confirmado' => true]);
        });

        return response()->json([
            'message' => 'Todos los artículos del vehículo han sido confirmados correctamente.'
        ]);
    }

    /**
     * 3. Listar artículos y otros de la semana actual (con paginación)
     */
    public function itemsSemanaActual(Request $request)
    {
        $userId = Auth::id();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Obtener artículos normales
        $articulos = TrabajoArticulo::where('tecnico_id', $userId)
            ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
            ->with([
                'articulo.subCategoria.categoria',
                'articulo.marca',
                'articulo.unidad',
                'articulo.presentacion',
                'trabajo.vehiculo'
            ])
            ->get();

        // Obtener "otros"
        $otros = TrabajoOtro::where('user_id', $userId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->with(['trabajo.vehiculo'])
            ->get();

        // Combinar y ordenar
        $todosLosItems = $this->combinarYOrdenar($articulos, $otros);

        // Paginación Manual
        $page = $request->get('page', 1);
        $perPage = 10;
        
        $paginatedItems = new LengthAwarePaginator(
            $todosLosItems->forPage($page, $perPage)->values(),
            $todosLosItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Transformar la colección paginada
        $data = $paginatedItems->getCollection()->map(function ($item) {
            return $this->formatearItem($item);
        });

        // Retornar estructura paginada personalizada
        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginatedItems->currentPage(),
                'last_page' => $paginatedItems->lastPage(),
                'per_page' => $paginatedItems->perPage(),
                'total' => $paginatedItems->total(),
            ]
        ]);
    }

    /**
     * 4. Confirmar todos los artículos de la semana actual
     */
    public function confirmarTodosSemana()
    {
        $userId = Auth::id();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        DB::transaction(function () use ($startOfWeek, $endOfWeek, $userId) {
            // Confirmar artículos normales
            TrabajoArticulo::where('tecnico_id', $userId)
                ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
                ->where('confirmado', false)
                ->update(['confirmado' => true]);

            // Confirmar "otros"
            TrabajoOtro::where('user_id', $userId)
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->where('confirmado', false)
                ->update(['confirmado' => true]);
        });

        return response()->json([
            'message' => 'Todos los artículos de la semana han sido confirmados.'
        ]);
    }

    /**
     * 5. Confirmar un artículo individualmente
     */
    public function confirmarArticuloIndividual(TrabajoArticulo $trabajoArticulo)
    {
        // Validación opcional: asegurar que el item pertenece al usuario autenticado
        if ($trabajoArticulo->tecnico_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $trabajoArticulo->confirmado = true;
        $trabajoArticulo->save();

        return response()->json([
            'message' => 'Artículo confirmado exitosamente',
            'id' => $trabajoArticulo->id,
            'confirmado' => true
        ]);
    }

    /**
     * 6. Confirmar un "otro" individualmente
     */
    public function confirmarOtroIndividual(TrabajoOtro $trabajoOtro)
    {
        // Validación opcional
        if ($trabajoOtro->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $trabajoOtro->confirmado = true;
        $trabajoOtro->save();

        return response()->json([
            'message' => 'Item confirmado exitosamente',
            'id' => $trabajoOtro->id,
            'confirmado' => true
        ]);
    }

    // ==========================================
    // MÉTODOS PRIVADOS DE AYUDA
    // ==========================================

    /**
     * Combina colecciones y ordena por fecha descendente
     */
    private function combinarYOrdenar($articulos, $otros)
    {
        return $articulos->concat($otros)->sortByDesc(function ($item) {
            if ($item instanceof TrabajoArticulo) {
                if ($item->fecha instanceof \DateTimeInterface) {
                    return $item->fecha;
                }
                return Carbon::parse($item->fecha);
            } else {
                return $item->created_at;
            }
        });
    }

    /**
     * Formatea un item para la respuesta JSON unificada
     * Replicando la lógica de construcción de etiquetas de la vista Blade
     */
    private function formatearItem($item)
    {
        $esOtro = $item instanceof TrabajoOtro;
        $label = '';
        $fecha = null;
        $hora = null;

        if ($esOtro) {
            $label = $item->descripcion;
            $fecha = $item->created_at;
            $hora = $item->created_at->format('H:i:s');
        } else {
            // Lógica para construir el nombre completo del artículo
            $articulo = $item->articulo;
            $parts = [];
            
            if ($articulo) {
                if ($articulo->categoria) $parts[] = $articulo->categoria->nombre;
                if ($articulo->marca) $parts[] = $articulo->marca->nombre;
                if ($articulo->subCategoria) $parts[] = $articulo->subCategoria->nombre;
                if ($articulo->especificacion) $parts[] = $articulo->especificacion;
                if ($articulo->presentacion) $parts[] = $articulo->presentacion->nombre;
                if ($articulo->medida) $parts[] = $articulo->medida;
                if ($articulo->unidad) $parts[] = $articulo->unidad->nombre;
                if ($articulo->color) $parts[] = $articulo->color;
            }
            
            $label = implode(' ', $parts);
            $fecha = $item->fecha;
            $hora = $item->hora;
        }

        // Obtener datos del vehículo si se cargó la relación
        $vehiculoInfo = null;
        if ($item->trabajo && $item->trabajo->vehiculo) {
            $vehiculoInfo = $item->trabajo->vehiculo->placa . ' - ' . ($item->trabajo->vehiculo->tipoVehiculo->nombre ?? '') . ' ' . ($item->trabajo->vehiculo->marca->nombre ?? '') . ' ' . ($item->trabajo->vehiculo->modelo->nombre ?? '');
        }

        return [
            'id' => $item->id,
            'tipo' => $esOtro ? 'otro' : 'articulo',
            'descripcion' => $label, // String ya construido para mostrar en frontend
            'cantidad' => $item->cantidad,
            'cantidad_fraccion' => \App\Services\FractionService::decimalToFraction($item->cantidad), // Asumiendo que tienes este servicio
            'fecha' => $fecha instanceof \Carbon\Carbon ? $fecha->isoFormat('dddd, D [de] MMMM') : $fecha,
            'hora' => \Carbon\Carbon::parse($hora)->format('h:i A'),
            'confirmado' => (bool)$item->confirmado,
            'vehiculo_resumen' => $vehiculoInfo,
            // Datos raw por si el frontend los necesita
            'raw_date' => $fecha,
        ];
    }
}