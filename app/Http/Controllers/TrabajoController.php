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
        // Obtener la fecha actual y la de ayer
        $fechaActual = now()->format('Y-m-d'); // Formatear para comparar solo la fecha
        $fechaAyer = now()->subDay()->format('Y-m-d'); // Fecha de ayer

        // Obtener trabajos que no tengan fecha_salida o cuya fecha_salida sea igual a la fecha actual o ayer
        $trabajos = Trabajo::where(function ($query) use ($fechaActual, $fechaAyer) {
            $query->whereNull('fecha_salida') // Filtra por trabajos sin fecha_salida
                ->orWhereDate('fecha_salida', $fechaActual) // Filtra por fecha_salida igual a la fecha actual
                ->orWhereDate('fecha_salida', $fechaAyer); // Filtra por fecha_salida igual a la fecha de ayer
        })
            ->whereDoesntHave('tecnicos', function ($query) {
                $query->where('tecnico_id', auth()->id()); // Filtra por trabajos no asignados al técnico actual
            })
            ->orderBy('created_at', 'desc')
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
        // Obtener la fecha actual
        $fechaActual = now()->format('Y-m-d'); // Formatear para comparar solo la fecha

        $user = auth()->user();

        if ($trabajo->fecha_salida !== null && $trabajo->fecha_salida < $fechaActual) {
            abort(403, 'Forbidden');
        }

        $trabajo->usuarios()->attach($user->id);

        return redirect()->route('home')->with('success', 'Trabajo asignado correctamente.');
    }

    /**
     * Finalizar un trabajo asignado.
     * 
     * @param \App\Models\Trabajo $trabajo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finalizar(Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios()->where('tecnico_id', $user->id)->exists()) {
            abort(403, 'Forbidden');
        }

        $trabajo->usuarios()->updateExistingPivot($user->id, ['finalizado' => true]);

        return redirect()->route('home')->with('success', 'Trabajo finalizado correctamente.');
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
