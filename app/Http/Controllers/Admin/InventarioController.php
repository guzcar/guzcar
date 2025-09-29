<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function edit(Trabajo $trabajo)
    {
        // Define los ítems fijos del checklist en el controlador
        $defaultItems = [
            'T. Propiedad', 'SOAT', 'Permiso Lunas', 'Carnet de Servicios', 'Manual Propietario',
            'Llavero', 'Seg. Ruedas', 'Seg. Vasos', 'Masc. Radio', 'Encendedor',
            'Pisos', 'Funda Asiento', 'Plumillas', 'Antena', 'Vasos Rueda',
            'Emblemas', 'Llantas Repuesto', 'Tapas Fluido', 'Kit Herramienta', 'Gata y Palanca'
        ];

        return view('admin.inventario.edit', compact('trabajo', 'defaultItems'));
    }

    public function update(Request $request, Trabajo $trabajo)
    {
        $request->validate([
            'checklist' => 'nullable|array',
            'checklist.*.nombre' => 'nullable|string|max:255',
            'combustible' => 'required|integer|min:0|max:100',
            'aceite' => 'required|integer|min:0|max:100',
            'observaciones' => 'nullable|string',
            'symbols' => 'nullable|string',
            'firma' => 'nullable|string',
        ]);

        $checklistData = $request->input('checklist', []);

        // Procesar y limpiar el checklist para asegurar la consistencia de los datos
        $processedChecklist = collect($checklistData)
            ->map(function ($item) {
                // Asegura que cada item tenga un valor booleano para 'checked'
                return [
                    'nombre' => $item['nombre'] ?? null,
                    'checked' => isset($item['checked']) && $item['checked'] == '1',
                ];
            })
            ->filter(function ($item) {
                // Filtrar items que no tienen nombre (nuevos pero dejados en blanco)
                return !empty($item['nombre']);
            })
            ->values() // Re-indexar el array
            ->all();

        // Preparar datos para guardar en JSON
        $inventarioData = [
            'checklist' => $processedChecklist,
            'combustible' => $request->combustible,
            'aceite' => $request->aceite,
            'observaciones' => $request->observaciones,
            'symbols' => json_decode($request->symbols, true) ?? [],
            'firma' => $request->firma,
        ];

        // Guardar en el campo JSON del modelo
        $trabajo->inventario_vehiculo_ingreso = $inventarioData;
        $trabajo->save();

        return redirect()->route('filament.admin.resources.trabajos.edit', ['record' => $trabajo])
            ->with('success', 'Inventario de vehículo actualizado correctamente.');
    }
}