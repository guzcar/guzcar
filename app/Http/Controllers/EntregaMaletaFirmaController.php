<?php

namespace App\Http\Controllers;

use App\Models\EntregaMaleta;
use App\Models\EntregaMaletaDetalle;
use Illuminate\Http\Request;

class EntregaMaletaFirmaController extends Controller
{
    public function index(EntregaMaleta $entrega)
    {
        $entrega->load(['maleta', 'propietario', 'responsable', 'detalles.herramienta']);
        
        // Obtenemos todas las herramientas
        $herramientas = $entrega->detalles->map(fn($d) => $d->herramienta);

        return view('entrega-maleta.firmar', [
            'entrega' => $entrega,
            'herramientas' => $herramientas,
            'titulo' => 'Firma de Entrega'
        ]);
    }

    public function seleccion(EntregaMaleta $entrega, string $detalles)
    {
        $ids = explode(',', $detalles);
        
        // Filtramos solo los detalles seleccionados para mostrarlos en la lista
        $detallesSeleccionados = EntregaMaletaDetalle::with('herramienta')
            ->whereIn('id', $ids)
            ->where('entrega_maleta_id', $entrega->id)
            ->get();

        $herramientas = $detallesSeleccionados->map(fn($d) => $d->herramienta);

        return view('entrega-maleta.firmar', [
            'entrega' => $entrega,
            'herramientas' => $herramientas,
            'titulo' => 'Firma de Entrega'
        ]);
    }

    public function save(Request $request, EntregaMaleta $entrega)
    {
        $request->validate([
            // Validamos que sean strings (Base64)
            'firma_propietario' => 'nullable|string',
            'firma_responsable' => 'nullable|string',
        ]);

        // Guardamos directamente el Base64 en la base de datos
        // Si el usuario limpió el pad, vendrá null o vacío, actualizamos acorde.
        $entrega->update([
            'firma_propietario' => $request->firma_propietario,
            'firma_responsable' => $request->firma_responsable,
        ]);

        return response()->json(['success' => true, 'message' => 'Firmas guardadas correctamente.']);
    }
}