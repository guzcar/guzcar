<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\TrabajoArticulo;
use App\Models\TrabajoOtro; // Agregar este modelo
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticuloController extends Controller
{
    public function trabajo(Trabajo $trabajo)
    {
        $user = auth()->user();

        $trabajo->load([
            'trabajoArticulos' => function ($query) {
                $query->where('tecnico_id', Auth::id())
                    ->with(['articulo.subCategoria.categoria'])
                    ->orderBy('fecha', 'desc')
                    ->orderBy('hora', 'desc');
            },
            'trabajoOtros' => function ($query) { // Agregar esta relación
                $query->where('user_id', Auth::id()) // Cambiar a user_id
                    ->orderBy('created_at', 'desc'); // Ordenar por created_at
            },
            'vehiculo.tipoVehiculo'
        ]);

        return view('articulos.trabajo', compact('trabajo'));
    }

    public function index()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Obtener artículos normales
        $articulos = TrabajoArticulo::where('tecnico_id', Auth::id())
            ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
            ->with([
                'articulo.subCategoria.categoria',
                'trabajo.vehiculo'
            ])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        // Obtener "otros" artículos - usar created_at para el rango de fecha
        $otros = TrabajoOtro::where('user_id', Auth::id()) // Cambiar a user_id
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->with(['trabajo.vehiculo'])
            ->orderBy('created_at', 'desc') // Ordenar por created_at
            ->get();

        // Combinar y ordenar - CORREGIDO
        $todosLosItems = $articulos->concat($otros)->sortByDesc(function ($item) {
            // Para artículos normales usar fecha (que ya incluye hora completa)
            if ($item instanceof TrabajoArticulo) {
                // Si fecha ya es un objeto Carbon/DateTime
                if ($item->fecha instanceof \DateTimeInterface) {
                    return $item->fecha;
                }
                // Si es string, parsearlo directamente
                return Carbon::parse($item->fecha);
            } else {
                // Para otros usar created_at
                return $item->created_at;
            }
        });

        // Para paginación manual
        $page = request()->get('page', 1);
        $perPage = 10;
        $paginatedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $todosLosItems->forPage($page, $perPage),
            $todosLosItems->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('articulos.index', compact('paginatedItems'));
    }

    // Métodos para confirmar artículos normales (ya existentes)
    public function confirmarTrabajo(TrabajoArticulo $trabajoArticulo)
    {
        $trabajoArticulo->confirmado = true;
        $trabajoArticulo->save();
        return back()->with('success', 'El artículo ha sido confirmado');
    }

    public function confirmarIndex(TrabajoArticulo $trabajoArticulo)
    {
        $trabajoArticulo->confirmado = true;
        $trabajoArticulo->save();
        return back()->with('success', 'El artículo ha sido confirmado');
    }

    // Nuevos métodos para confirmar "otros"
    public function confirmarTrabajoOtro(TrabajoOtro $trabajoOtro)
    {
        $trabajoOtro->confirmado = true;
        $trabajoOtro->save();
        return back()->with('success', 'El artículo ha sido confirmado');
    }

    public function confirmarIndexOtro(TrabajoOtro $trabajoOtro)
    {
        $trabajoOtro->confirmado = true;
        $trabajoOtro->save();
        return back()->with('success', 'El artículo ha sido confirmado');
    }

    public function confirmarTrabajoTodos(Request $request, Trabajo $trabajo)
    {
        DB::transaction(function () use ($trabajo) {
            // Confirmar artículos normales
            $trabajo->trabajoArticulos()
                ->where('tecnico_id', Auth::id())
                ->update(['confirmado' => true]);

            // Confirmar "otros" artículos - usar user_id
            $trabajo->trabajoOtros()
                ->where('user_id', Auth::id())
                ->update(['confirmado' => true]);
        });

        return back()->with('success', 'Todos los artículos han sido confirmados correctamente.');
    }

    public function confirmarIndexTodos(Request $request)
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        DB::transaction(function () use ($startOfWeek, $endOfWeek) {
            // Confirmar artículos normales
            TrabajoArticulo::where('tecnico_id', Auth::id())
                ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
                ->update(['confirmado' => true]);

            // Confirmar "otros" artículos - usar user_id y created_at
            TrabajoOtro::where('user_id', Auth::id())
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->update(['confirmado' => true]);
        });

        return back()->with('success', 'Todos los artículos de esta semana han sido confirmados correctamente.');
    }
}