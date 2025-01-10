<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenciaController extends Controller
{
    /**
     * Mostrar todas las evidencias del trabajo actual que pertenecen al usuario.
     */
    public function index(Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios->contains(auth()->user()) || $trabajo->fecha_salida !== null) {
            abort(403, 'Forbidden');
        }

        $evidencias = Evidencia::where('trabajo_id', $trabajo->id)
            ->where('user_id', $user->id)
            ->get();

        return view('evidencias.index', compact('evidencias', 'trabajo'));
    }

    /**
     * Subir una nueva evidencia.
     */
    public function store(Request $request, Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios()->where('mecanico_id', $user->id)->exists()) {
            abort(403, 'Forbidden');
        }

        $request->validate([
            'evidencia' => 'required|file|mimes:jpg,jpeg,png,mp4,mov',
            'observacion' => 'nullable|string|max:255',
        ]);

        $file = $request->file('evidencia');
        $path = $file->store('evidencia', 'public');

        Evidencia::create([
            'trabajo_id' => $trabajo->id,
            'user_id' => $user->id,
            'evidencia_url' => $path,
            'tipo' => $file->getMimeType() === 'video/mp4' ? 'video' : 'imagen',
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('evidencias.index', $trabajo)->with('success', 'Evidencia subida correctamente.');
    }

    /**
     * Actualizar una evidencia existente.
     */
    public function update(Request $request, Trabajo $trabajo, Evidencia $evidencia)
    {
        $user = auth()->user();

        if ($evidencia->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $request->validate([
            'observacion' => 'nullable|string|max:255',
            'evidencia' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:10240',
        ]);

        if ($request->hasFile('evidencia')) {
            Storage::disk('public')->delete($evidencia->evidencia_url);
            $file = $request->file('evidencia');
            $path = $file->store('evidencia', 'public');
            $evidencia->evidencia_url = $path;
            $evidencia->tipo = $file->getMimeType() === 'video/mp4' ? 'video' : 'imagen';
        }

        $evidencia->observacion = $request->observacion;
        $evidencia->save();

        return redirect()->route('evidencias.index', $trabajo)->with('success', 'Evidencia actualizada correctamente.');
    }

    /**
     * Eliminar una evidencia.
     */
    public function destroy(Trabajo $trabajo, Evidencia $evidencia)
    {
        $user = auth()->user();

        if ($evidencia->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        Storage::disk('public')->delete($evidencia->evidencia_url);
        $evidencia->delete();

        return redirect()->route('evidencias.index', $trabajo)->with('success', 'Evidencia eliminada correctamente.');
    }
}
