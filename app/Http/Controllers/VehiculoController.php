<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Articulo;
use App\Models\Servicio;

class VehiculoController extends Controller
{
    public function consultaVehicular()
    {
        return view('vehiculos.consulta_vehicular');
    }

    public function buscarVehiculo(Request $request)
    {
        // Obtener la placa desde la URL (query parameter)
        $placa = $request->query('placa');
        $vehiculo = Vehiculo::where('placa', $placa)
            ->first();

        $trabajos = $vehiculo
            ? $vehiculo->trabajos()
                ->orderByDesc('fecha_ingreso')
                ->get()
            : collect();

        return view('vehiculos.consulta_vehicular', compact('placa', 'vehiculo', 'trabajos'));
    }

    public function articulosUtilizados($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        // Obtener artículos únicos directamente desde la tabla de artículos
        $articulos = Articulo::whereHas('trabajos', function ($query) use ($vehiculo) {
            $query->where('vehiculo_id', $vehiculo->id);
        })
            ->with('subCategoria.categoria')
            ->distinct()
            ->get();

        return view('vehiculos.articulos_utilizados', compact('articulos', 'vehiculo'));
    }

    public function serviciosEjecutados($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        // Obtener servicios únicos directamente desde la tabla de servicios
        $servicios = Servicio::whereHas('trabajos', function ($query) use ($vehiculo) {
            $query->where('vehiculo_id', $vehiculo->id);
        })
            ->distinct()
            ->get();

        return view('vehiculos.servicios_ejecutados', compact('servicios', 'vehiculo'));
    }
}
