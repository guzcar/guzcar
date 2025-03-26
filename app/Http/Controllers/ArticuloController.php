<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\TrabajoArticulo;
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
            'vehiculo.tipoVehiculo'
        ]);

        return view('articulos.trabajo', compact('trabajo'));
    }

    public function index()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $articulos = TrabajoArticulo::where('tecnico_id', Auth::id())
            ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
            ->with([
                'articulo.subCategoria.categoria',
                'trabajo.vehiculo'
            ])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(10);

        return view('articulos.index', compact('articulos'));
    }

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

    public function confirmarTrabajoTodos(Request $request, Trabajo $trabajo)
    {
        // Obtener los IDs de los artículos que pertenecen al usuario autenticado y al trabajo actual
        $articulosIds = $trabajo->trabajoArticulos()
            ->where('tecnico_id', Auth::id())
            ->pluck('id');

        // Validar que haya artículos para confirmar
        if ($articulosIds->isEmpty()) {
            return back()->with('error', 'No hay artículos para confirmar.');
        }

        // Confirmar todos los artículos
        DB::transaction(function () use ($articulosIds) {
            TrabajoArticulo::whereIn('id', $articulosIds)->update(['confirmado' => true]);
        });

        // Redireccionar con un mensaje de éxito
        return back()->with('success', 'Todos los artículos han sido confirmados correctamente.');
    }

    public function confirmarIndexTodos(Request $request)
    {
        // Obtener el inicio y el fin de la semana actual
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // Obtener los IDs de los artículos que pertenecen al usuario autenticado y están dentro de la semana actual
        $articulosIds = TrabajoArticulo::where('tecnico_id', Auth::id())
            ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
            ->pluck('id');

        // Validar que haya artículos para confirmar
        if ($articulosIds->isEmpty()) {
            return back()->with('error', 'No hay artículos para confirmar esta semana.');
        }

        // Confirmar todos los artículos
        DB::transaction(function () use ($articulosIds) {
            TrabajoArticulo::whereIn('id', $articulosIds)->update(['confirmado' => true]);
        });

        // Redireccionar con un mensaje de éxito
        return back()->with('success', 'Todos los artículos de esta semana han sido confirmados correctamente.');
    }
}
