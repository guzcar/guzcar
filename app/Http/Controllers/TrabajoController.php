<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use Illuminate\Http\Request;

class TrabajoController extends Controller
{
    /**
     * Mostrar los trabajos disponibles para asignación.
     * 
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function asignarTrabajos()
    {
        $trabajos = Trabajo::whereNull('fecha_salida')
            ->whereDoesntHave('tecnicos', function ($query) {
                $query->where('tecnico_id', auth()->id());
            })
            ->get();

        return view('trabajos.asignar', compact('trabajos'));
    }

    /**
     * Asignar un trabajo al usuario actual.
     * 
     * @param \App\Models\Trabajo $trabajo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignar(Trabajo $trabajo)
    {
        $user = auth()->user();

        if ($trabajo->fecha_salida !== null) {
            abort(403, 'Forbidden');
        }

        $trabajo->usuarios()->attach($user->id);

        return redirect()->route('home')->with('success', 'Trabajo asignado correctamente.');
    }

    /**
     * Abandonar un trabajo asignado.
     * 
     * @param \App\Models\Trabajo $trabajo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function abandonar(Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios()->where('tecnico_id', $user->id)->exists()) {
            abort(403, 'Forbidden');
        }

        $trabajo->usuarios()->detach($user->id);

        return redirect()->route('home')->with('success', 'Has abandonado el trabajo.');
    }
}
