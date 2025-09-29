<?php

namespace App\Http\Controllers\Admin\Pdf;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Trabajo;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function ingreso(Trabajo $trabajo) {
        // Obtener datos del inventario
        $inventarioData = $trabajo->inventario_vehiculo_ingreso ?? [];
        $checklist = collect($inventarioData['checklist'] ?? []);
        
        // Separar items default y custom
        $defaultItems = [
            'T. Propiedad', 'SOAT', 'Permiso Lunas', 'Carnet de Servicios', 'Manual Propietario',
            'Llavero', 'Seg. Ruedas', 'Seg. Vasos', 'Masc. Radio', 'Encendedor',
            'Pisos', 'Funda Asiento', 'Plumillas', 'Antena', 'Vasos Rueda',
            'Emblemas', 'Llantas Repuesto', 'Tapas Fluido', 'Kit Herramienta', 'Gata y Palanca'
        ];
        
        $itemsDefault = $checklist->whereIn('nombre', $defaultItems);
        $itemsCustom = $checklist->whereNotIn('nombre', $defaultItems);
        
        // Preparar datos para la vista
        $data = [
            'trabajo' => $trabajo,
            'checklist' => $checklist,
            'itemsDefault' => $itemsDefault,
            'itemsCustom' => $itemsCustom,
            'combustible' => $inventarioData['combustible'] ?? 50,
            'aceite' => $inventarioData['aceite'] ?? 50,
            'observaciones' => $inventarioData['observaciones'] ?? '',
            'symbols' => $inventarioData['symbols'] ?? [],
            'firma' => $inventarioData['firma'] ?? null,
            'fecha' => now()->format('d/m/Y H:i'),
        ];
        
        $pdf = Pdf::loadView('admin.pdf.ingreso', $data);
        
        // Generar nombre del archivo
        $filename = "checklist_ingreso_{$trabajo->vehiculo->placa}_{$trabajo->id}.pdf";
        
        return $pdf->stream($filename);
    }
}