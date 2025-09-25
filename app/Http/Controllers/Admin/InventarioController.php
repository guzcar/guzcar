<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function edit(Trabajo $trabajo)
    {
        return view('admin.inventario.edit', compact('trabajo'));
    }

    public function update(Request $request, Trabajo $trabajo)
    {
        $request->validate([
            'checklist' => 'nullable|array',
            'checklist.*.nombre' => 'required|string',
            'combustible' => 'required|integer|min:0|max:100',
            'aceite' => 'required|integer|min:0|max:100',
            'observaciones' => 'nullable|string',
            'symbols' => 'nullable|string',
            'firma' => 'nullable|string', // Nuevo campo para la firma
        ]);

        // Preparar datos para guardar
        $inventarioData = [
            'checklist' => $request->checklist ?? [],
            'combustible' => $request->combustible,
            'aceite' => $request->aceite,
            'observaciones' => $request->observaciones,
            'symbols' => json_decode($request->symbols, true) ?? [],
            'firma' => $request->firma, // Guardar la firma
        ];

        // Guardar en el campo correcto
        $trabajo->inventario_vehiculo_ingreso = $inventarioData;
        $trabajo->save();

        return redirect()->route('filament.admin.resources.trabajos.edit', ['record' => $trabajo])
            ->with('success', 'Inventario de vehículo actualizado correctamente.');
    }
}