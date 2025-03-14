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

        // Obtener la fecha actual
        $fechaActual = now()->format('Y-m-d'); // Formatear para comparar solo la fecha

        // Obtener trabajos asignados al usuario con fecha_salida == null o fecha_salida == fecha actual
        $trabajos = $user->trabajos()
            ->orderBy('created_at', 'desc')
            ->where(function ($query) use ($fechaActual) {
                $query->whereNull('fecha_salida') // Filtra por trabajos sin fecha_salida
                    ->orWhereDate('fecha_salida', '>=', $fechaActual); // Filtra por fecha_salida igual a la fecha actual
            })
            ->wherePivot('finalizado', false) // Filtra por finalizado == false en la tabla intermedia
            ->get();

        return view('home', compact('trabajos'));
    }
}
