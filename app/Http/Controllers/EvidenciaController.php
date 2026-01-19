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
     * 
     * @param \App\Models\Trabajo $trabajo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios->contains($user)) {
            abort(403, 'Forbidden');
        }

        $evidencias = Evidencia::where('trabajo_id', $trabajo->id)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('evidencias.index', compact('evidencias', 'trabajo'));
    }

    public function all(Trabajo $trabajo)
    {
        $evidencias = $trabajo->evidencias()->orderBy('created_at', 'desc')->paginate(10);
        return view('evidencias.all', compact('trabajo', 'evidencias'));
    }

    /**
     * Subir una nueva evidencia.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Trabajo $trabajo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios()->where('tecnico_id', $user->id)->exists()) {
            abort(403, 'Forbidden');
        }

        $request->validate([
            'evidencias' => 'required|array',
            'evidencias.*' => 'file',
            'observacion' => 'nullable|string',
        ]);

        $files = $request->file('evidencias');
        $observacion = $request->observacion;

        foreach ($files as $index => $file) {
            $path = $file->store('evidencia', 'public');

            Evidencia::create([
                'trabajo_id' => $trabajo->id,
                'user_id' => $user->id,
                'evidencia_url' => $path,
                'tipo' => str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'imagen',
                'observacion' => $observacion
            ]);
        }

        return redirect()->route('gestion.evidencias.index', $trabajo)->with('success', 'Evidencias subidas correctamente.');
    }

    /**
     * Actualizar una evidencia existente.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Trabajo $trabajo
     * @param \App\Models\Evidencia $evidencia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Trabajo $trabajo, Evidencia $evidencia)
    {
        $user = auth()->user();

        if ($evidencia->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $request->validate([
            'observacion' => 'nullable|string',
        ]);

        $evidencia->observacion = $request->observacion;
        $evidencia->save();

        return redirect()->route('gestion.evidencias.index', $trabajo)->with('success', 'Evidencia actualizada correctamente.');
    }

    /**
     * Eliminar una evidencia.
     * 
     * @param \App\Models\Trabajo $trabajo
     * @param \App\Models\Evidencia $evidencia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Trabajo $trabajo, Evidencia $evidencia)
    {
        $user = auth()->user();

        if ($evidencia->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        Storage::disk('public')->delete($evidencia->evidencia_url);
        $evidencia->delete();

        return redirect()->route('gestion.evidencias.index', $trabajo)->with('success', 'Evidencia eliminada correctamente.');
    }

    public function bulkUpdate(Request $request, Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios->contains($user)) {
            abort(403, 'Forbidden');
        }

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:evidencias,id',
            'observacion' => 'nullable|string',
        ]);

        $ids = $validated['ids'];
        $observacion = ($request->has('observacion') && $request->input('observacion') !== '')
            ? $request->input('observacion')
            : null;

        $query = Evidencia::where('trabajo_id', $trabajo->id)
            ->where('user_id', $user->id)
            ->whereIn('id', $ids);

        $totalEncontradas = (clone $query)->count();
        $actualizadas = $query->update(['observacion' => $observacion]);

        return redirect()
            ->route('gestion.evidencias.index', $trabajo)
            ->with('success', "Se actualizaron {$actualizadas} de {$totalEncontradas} evidencias seleccionadas.");
    }

    public function bulkDestroy(Request $request, Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios->contains($user)) {
            abort(403, 'Forbidden');
        }

        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:evidencias,id',
        ]);

        $ids = $validated['ids'];

        $evidencias = Evidencia::where('trabajo_id', $trabajo->id)
            ->where('user_id', $user->id)
            ->whereIn('id', $ids)
            ->get();

        $totalEncontradas = $evidencias->count();

        foreach ($evidencias as $ev) {
            Storage::disk('public')->delete($ev->evidencia_url);
            $ev->delete();
        }

        return redirect()
            ->route('gestion.evidencias.index', $trabajo)
            ->with('success', "Se eliminaron {$totalEncontradas} evidencias seleccionadas.");
    }
}