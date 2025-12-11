<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use App\Models\TrabajoDescripcionTecnico;
use Illuminate\Http\Request;

class TrabajoDescripcionApiController extends Controller
{
    /**
     * Listar descripciones del técnico actual
     */
    public function index(Trabajo $trabajo)
    {
        $descripciones = $trabajo->descripcionTecnicos()
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Descripciones obtenidas correctamente',
            'data' => $descripciones
        ]);
    }

    /**
     * Registrar descripción
     */
    public function store(Request $request, Trabajo $trabajo)
    {
        $request->validate([
            'descripcion' => 'required|string'
        ]);

        $d = TrabajoDescripcionTecnico::create([
            'trabajo_id' => $trabajo->id,
            'user_id' => auth()->id(),
            'descripcion' => $request->descripcion
        ]);

        return response()->json([
            'message' => 'Descripción registrada correctamente',
            'data' => $d
        ], 201);
    }

    /**
     * Editar descripción
     */
    public function update(Request $request, TrabajoDescripcionTecnico $descripcion)
    {
        if ($descripcion->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'descripcion' => 'required|string'
        ]);

        $descripcion->update([
            'descripcion' => $request->descripcion
        ]);

        return response()->json([
            'message' => 'Descripción actualizada correctamente',
            'data' => $descripcion
        ]);
    }

    /**
     * Eliminar descripción
     */
    public function destroy(TrabajoDescripcionTecnico $descripcion)
    {
        if ($descripcion->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $descripcion->delete();

        return response()->json([
            'message' => 'Descripción eliminada correctamente'
        ]);
    }
}
