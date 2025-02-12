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

        // Obtener trabajos asignados al usuario con fecha_salida == null
        $trabajos = $user->trabajos()->whereNull('fecha_salida')->get();

        return view('home', compact('trabajos'));
    }
}
