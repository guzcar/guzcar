<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Mostrar la tabla de trabajos asignados al usuario actual.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
{
    $user = auth()->user();

    // Fechas para comparación
    $hoy = now()->format('Y-m-d');
    $ayer = now()->subDay()->format('Y-m-d');

    // Primero: Solo mis vehículos asignados no finalizados
    $trabajos = $user->trabajos()
        ->wherePivot('finalizado', false)
        ->where(function ($query) use ($hoy, $ayer) {
            // Segundo: De esos, solo los que cumplen condiciones de fecha o disponibilidad
            $query->whereNull('fecha_salida')
                ->orWhereDate('fecha_salida', $hoy)
                ->orWhereDate('fecha_salida', $ayer)
                ->orWhere('disponible', true); // Permiso especial
        })
        ->orderBy('trabajo_tecnicos.created_at', 'desc') // Ordenar por created_at de la tabla intermedia
        ->get();

    return view('home', compact('trabajos'));
}
}
