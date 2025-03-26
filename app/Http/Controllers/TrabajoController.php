<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use Illuminate\Http\Request;

class TrabajoController extends Controller
{
    /**
     * Mostrar los trabajos disponibles para asignación.
     */
    public function asignarTrabajos()
    {
        $hoy = now()->format('Y-m-d');
        $ayer = now()->subDay()->format('Y-m-d');

        $trabajos = Trabajo::whereDoesntHave('tecnicos', function ($query) {
            $query->where('tecnico_id', auth()->id());
        })
            ->where(function ($query) use ($hoy, $ayer) {
                $query->whereNull('fecha_salida')
                    ->orWhereDate('fecha_salida', $hoy)
                    ->orWhereDate('fecha_salida', $ayer)
                    ->orWhere('disponible', true);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('trabajos.asignar', compact('trabajos'));
    }

    /**
     * Asignar un trabajo al usuario actual.
     */
    public function asignar(Trabajo $trabajo)
    {
        $hoy = now()->format('Y-m-d');
        $ayer = now()->subDay()->format('Y-m-d');

        $puedeAsignar = Trabajo::where('id', $trabajo->id)
            ->whereDoesntHave('tecnicos', function ($query) {
                $query->where('tecnico_id', auth()->id());
            })
            ->where(function ($query) use ($hoy, $ayer) {
                $query->whereNull('fecha_salida')
                    ->orWhereDate('fecha_salida', $hoy)
                    ->orWhereDate('fecha_salida', $ayer)
                    ->orWhere('disponible', true);
            })
            ->exists();

        if (!$puedeAsignar) {
            abort(403, 'No tienes permiso para asignar este trabajo');
        }

        auth()->user()->trabajos()->attach($trabajo->id, [
            'finalizado' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('home')->with('success', 'Trabajo asignado correctamente');
    }

    /**
     * Finalizar un trabajo asignado.
     */
    public function finalizar(Trabajo $trabajo)
    {
        // Verificar si el trabajo está asignado y no finalizado
        $trabajoAsignado = auth()->user()->trabajos()
            ->where('trabajo_id', $trabajo->id)
            ->wherePivot('finalizado', false)
            ->exists();

        if (!$trabajoAsignado) {
            abort(403, 'No puedes finalizar un trabajo no asignado o ya finalizado');
        }

        auth()->user()->trabajos()->updateExistingPivot($trabajo->id, [
            'finalizado' => true,
            'updated_at' => now()
        ]);

        return redirect()->route('home')->with('success', 'Trabajo finalizado correctamente');
    }

    /**
     * Abandonar un trabajo asignado.
     */
    public function abandonar(Trabajo $trabajo)
    {
        // Verificar si el trabajo está asignado y no finalizado
        // $trabajoAsignado = auth()->user()->trabajos()
        //     ->where('trabajo_id', $trabajo->id)
        //     ->wherePivot('finalizado', false)
        //     ->exists();

        // if (!$trabajoAsignado) {
        //     abort(403, 'No puedes abandonar un trabajo no asignado o ya finalizado');
        // }

        auth()->user()->trabajos()->detach($trabajo->id);

        return redirect()->route('home')->with('success', 'Has abandonado el trabajo');
    }
}
