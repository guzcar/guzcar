<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use App\Models\TrabajoDescripcionTecnico;
use Illuminate\Http\Request;

class TrabajoDescripcionTecnicoController extends Controller
{
    /**
     * Lista los detalles del técnico autenticado para el trabajo.
     */
    public function index(Trabajo $trabajo)
    {
        $user = auth()->user();

        // Debe ser técnico asignado al trabajo
        if (!$trabajo->usuarios()->where('tecnico_id', $user->id)->exists()) {
            abort(403, 'Forbidden');
        }

        // Para cabecera (placa)
        $trabajo->load('vehiculo');

        $detalles = TrabajoDescripcionTecnico::where('trabajo_id', $trabajo->id)
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('detalles.index', compact('trabajo', 'detalles'));
    }

    /**
     * Crea un nuevo detalle del técnico autenticado.
     */
    public function store(Request $request, Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios()->where('tecnico_id', $user->id)->exists()) {
            abort(403, 'Forbidden');
        }

        $validated = $request->validate([
            'descripcion' => 'required',
        ]);

        TrabajoDescripcionTecnico::create([
            'trabajo_id'  => $trabajo->id,
            'user_id'     => $user->id,
            'descripcion' => $validated['descripcion'],
        ]);

        return redirect()
            ->route('gestion.detalles.index', $trabajo)
            ->with('success', 'Detalle agregado correctamente.');
    }

    /**
     * Actualiza un detalle propio del técnico.
     */
    public function update(Request $request, Trabajo $trabajo, TrabajoDescripcionTecnico $detalle)
    {
        $user = auth()->user();

        // Verifica que el detalle sea del usuario y del trabajo
        if ($detalle->user_id !== $user->id || $detalle->trabajo_id !== $trabajo->id) {
            abort(403, 'Forbidden');
        }

        $validated = $request->validate([
            'descripcion' => 'required',
        ]);

        $detalle->update([
            'descripcion' => $validated['descripcion'],
        ]);

        return redirect()
            ->route('gestion.detalles.index', $trabajo)
            ->with('success', 'Detalle actualizado correctamente.');
    }

    /**
     * Elimina un detalle propio del técnico.
     */
    public function destroy(Trabajo $trabajo, TrabajoDescripcionTecnico $detalle)
    {
        $user = auth()->user();

        if ($detalle->user_id !== $user->id || $detalle->trabajo_id !== $trabajo->id) {
            abort(403, 'Forbidden');
        }

        $detalle->delete();

        return redirect()
            ->route('gestion.detalles.index', $trabajo)
            ->with('success', 'Detalle eliminado correctamente.');
    }
}
