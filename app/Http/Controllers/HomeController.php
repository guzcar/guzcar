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

        // Obtener la fecha actual y la de ayer
        $fechaActual = now()->format('Y-m-d'); // Formatear para comparar solo la fecha
        $fechaAyer = now()->subDay()->format('Y-m-d'); // Fecha de ayer

        // Obtener trabajos asignados al usuario con fecha_salida == null, hoy o ayer
        $trabajos = $user->trabajos()
            ->orderBy('created_at', 'desc')
            ->where(function ($query) use ($fechaActual, $fechaAyer) {
                $query->whereNull('fecha_salida') // Filtra por trabajos sin fecha_salida
                    ->orWhereDate('fecha_salida', $fechaActual) // Filtra por fecha_salida igual a la fecha actual
                    ->orWhereDate('fecha_salida', $fechaAyer); // Filtra por fecha_salida igual a la fecha de ayer
            })
            ->wherePivot('finalizado', false) // Filtra por finalizado == false en la tabla intermedia
            ->get();

        return view('home', compact('trabajos'));
    }
}
