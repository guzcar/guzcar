<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\TrabajoOtro;
use Illuminate\Http\Request;

class TrabajoRepuestoController extends Controller
{
    public function index(Trabajo $trabajo)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Acceso denegado.');
        }

        // SOLO carga los repuestos que pertenecen al usuario logueado
        $repuestos = TrabajoOtro::where('trabajo_id', $trabajo->id)
            ->where('creador_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('repuestos.index', compact('trabajo', 'repuestos'));
    }

    public function store(Request $request, Trabajo $trabajo)
    {
        if (!auth()->user()->is_admin) {
            abort(403, 'Acceso denegado.');
        }

        $request->validate([
            'descripcion' => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'cantidad'    => 'required|integer|min:1',
        ]);

        TrabajoOtro::create([
            'trabajo_id'  => $trabajo->id,
            'user_id'     => auth()->id(),
            'creador_id'  => auth()->id(),
            'descripcion' => $request->descripcion,
            'precio'      => $request->precio,
            'cantidad'    => $request->cantidad,
            'confirmado'  => true,
        ]);

        return redirect()->route('gestion.repuestos.index', $trabajo)
                         ->with('success', 'Repuesto registrado correctamente.');
    }

    public function update(Request $request, Trabajo $trabajo, TrabajoOtro $repuesto)
    {
        // Verificar que sea admin y que el repuesto le pertenezca
        if (!auth()->user()->is_admin || $repuesto->creador_id !== auth()->id()) {
            abort(403, 'Acceso denegado.');
        }

        $request->validate([
            'descripcion' => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'cantidad'    => 'required|integer|min:1',
        ]);

        $repuesto->update([
            'descripcion' => $request->descripcion,
            'precio'      => $request->precio,
            'cantidad'    => $request->cantidad,
        ]);

        return redirect()->route('gestion.repuestos.index', $trabajo)
                         ->with('success', 'Repuesto actualizado correctamente.');
    }

    public function destroy(Trabajo $trabajo, TrabajoOtro $repuesto)
    {
        if (!auth()->user()->is_admin || $repuesto->creador_id !== auth()->id()) {
            abort(403, 'Acceso denegado.');
        }

        $repuesto->delete();

        return redirect()->route('gestion.repuestos.index', $trabajo)
                         ->with('success', 'Repuesto eliminado.');
    }
}