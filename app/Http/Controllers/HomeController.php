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

        // Obtener trabajos asignados al usuario con fecha_salida == null y finalizado == false
        $trabajos = $user->trabajos()
            ->orderBy('created_at', 'desc')
            ->whereNull('fecha_salida') // Filtra por trabajos sin fecha_salida
            ->wherePivot('finalizado', false) // Filtra por finalizado == false en la tabla intermedia
            ->get();

        return view('home', compact('trabajos'));
    }
}
