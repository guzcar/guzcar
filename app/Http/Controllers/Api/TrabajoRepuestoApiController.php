<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use App\Models\TrabajoOtro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrabajoRepuestoApiController extends Controller
{
    public function index(Trabajo $trabajo)
    {
        if (!auth()->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: Solo administradores pueden gestionar repuestos.'
            ], 403);
        }

        $repuestos = TrabajoOtro::where('trabajo_id', $trabajo->id)
            ->where('creador_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $repuestos
        ]);
    }

    public function store(Request $request, Trabajo $trabajo)
    {
        if (!auth()->user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: Solo administradores pueden registrar repuestos.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'cantidad'    => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        // CREACIÓN CORREGIDA
        $repuesto = TrabajoOtro::create([
            'trabajo_id'  => $trabajo->id,
            'user_id'     => auth()->id(),
            'creador_id'  => auth()->id(),
            'descripcion' => $request->descripcion,
            'precio'      => $request->precio,
            'cantidad'    => $request->cantidad,
            'confirmado'  => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Repuesto registrado correctamente.',
            'data' => $repuesto
        ], 201);
    }

    public function update(Request $request, TrabajoOtro $repuesto)
    {
        if (!auth()->user()->is_admin || $repuesto->creador_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: No tienes permiso para editar este repuesto.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string|max:255',
            'precio'      => 'required|numeric|min:0',
            'cantidad'    => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $repuesto->update([
            'descripcion' => $request->descripcion,
            'precio'      => $request->precio,
            'cantidad'    => $request->cantidad,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Repuesto actualizado correctamente.',
            'data' => $repuesto
        ]);
    }

    public function destroy(TrabajoOtro $repuesto)
    {
        if (!auth()->user()->is_admin || $repuesto->creador_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: No tienes permiso para eliminar este repuesto.'
            ], 403);
        }

        $repuesto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Repuesto eliminado correctamente.'
        ]);
    }
}