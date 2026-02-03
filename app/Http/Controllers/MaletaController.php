<?php

namespace App\Http\Controllers;

use App\Models\Maleta;
use Illuminate\Http\Request;

class MaletaController extends Controller
{
    /**
     * Muestra la lista de maletas asignadas al usuario logueado.
     */
    public function index()
    {
        // Obtenemos solo las maletas donde el propietario es el usuario actual
        $maletas = Maleta::where('propietario_id', auth()->id())->get();

        return view('maletas.index', compact('maletas'));
    }

    /**
     * Muestra el detalle de una maleta específica (lista de herramientas).
     */
    public function show(Maleta $maleta)
    {
        // Seguridad: Verificar que la maleta pertenezca al usuario logueado
        // Si quieres que un admin vea todas, podrías agregar una condición extra aquí.
        if ($maleta->propietario_id !== auth()->id()) {
            abort(403, 'No tienes permiso para ver esta maleta.');
        }

        // Cargar los detalles junto con la información de la herramienta para optimizar la consulta (Eager Loading)
        $detalles = $maleta->detalles()->with('herramienta')->get();

        return view('maletas.show', compact('maleta', 'detalles'));
    }
}