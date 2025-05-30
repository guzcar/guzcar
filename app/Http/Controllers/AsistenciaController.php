<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index()
    {
        return view('asistencias.registro');
    }

    public function registrar(Request $request)
    {
        $userLat = $request->input('lat');
        $userLng = $request->input('lng');

        // Coordenadas del lugar permitido
        $targetLat = -9.077225437576932;
        $targetLng = -78.58168884609415;
        $radioPermitido = 75; // en metros

        $distancia = $this->calcularDistancia($userLat, $userLng, $targetLat, $targetLng);

        if ($distancia > $radioPermitido) {
            return response()->json(['message' => 'Estás fuera del área permitida.'], 403);
        }

        Asistencia::create([
            'user_id' => auth()->id(),
            'lat' => $userLat,
            'lng' => $userLng,
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Asistencia registrada.']);
    }

    private function calcularDistancia($lat1, $lon1, $lat2, $lon2)
    {
        $radioTierra = 6371000; // metros
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radioTierra * $c;
    }
}
