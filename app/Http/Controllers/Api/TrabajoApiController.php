<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Trabajo;

class TrabajoApiController extends Controller
{
    /**
     * Listar trabajos disponibles para asignación.
     */
    public function disponibles()
    {
        $hoy = now()->format('Y-m-d');
        $ayer = now()->subDay()->format('Y-m-d');
        $userId = auth()->id();

        $trabajos = Trabajo::whereDoesntHave('tecnicos', function ($query) use ($userId) {
            $query->where('tecnico_id', $userId);
        })
            ->where(function ($query) use ($hoy, $ayer) {
                $query->whereNull('fecha_salida')
                    ->orWhereDate('fecha_salida', $hoy)
                    ->orWhereDate('fecha_salida', $ayer)
                    ->orWhere('disponible', true);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Trabajos disponibles obtenidos correctamente',
            'data' => $trabajos->map(function ($t) {
                return [
                    'id' => $t->id,
                    'vehiculo' => [
                        'placa' => $t->vehiculo->placa ?? 'SIN PLACA',
                        'tipo' => $t->vehiculo->tipoVehiculo->nombre ?? null,
                        'marca' => $t->vehiculo->marca->nombre ?? null,
                        'modelo' => $t->vehiculo->modelo->nombre ?? null,
                        'color' => $t->vehiculo->color ?? null,
                    ],
                    'descripcion_servicio' => $t->descripcion_servicio,
                    'fecha_ingreso' => $t->fecha_ingreso,
                    'fecha_salida' => $t->fecha_salida,
                    'estado' => [
                        'asignado_al_tecnico' => false,
                        'finalizado' => false,
                    ],
                ];
            })
        ]);
    }

    /**
     * Listar trabajos asignados al usuario autenticado.
     */
    public function asignados()
    {
        $user = auth()->user();

        $hoy = now()->format('Y-m-d');
        $ayer = now()->subDay()->format('Y-m-d');

        // Buscar trabajos asignados al usuario (no finalizados)
        $trabajos = $user->trabajos()
            ->wherePivot('finalizado', false)
            ->where(function ($query) use ($hoy, $ayer) {
                $query->whereNull('fecha_salida')
                    ->orWhereDate('fecha_salida', $hoy)
                    ->orWhereDate('fecha_salida', $ayer)
                    ->orWhere('disponible', true);
            })
            ->orderBy('trabajo_tecnicos.created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Trabajos asignados obtenidos correctamente',
            'data' => $trabajos->map(function ($t) {
                return [
                    'id' => $t->id,
                    'vehiculo' => [
                        'placa' => $t->vehiculo->placa ?? 'SIN PLACA',
                        'tipo' => $t->vehiculo->tipoVehiculo->nombre ?? null,
                        'marca' => $t->vehiculo->marca->nombre ?? null,
                        'modelo' => $t->vehiculo->modelo->nombre ?? null,
                        'color' => $t->vehiculo->color ?? null,
                    ],
                    'descripcion_servicio' => $t->descripcion_servicio,
                    'fecha_ingreso' => $t->fecha_ingreso,
                    'fecha_salida' => $t->fecha_salida,
                    'estado' => [
                        'asignado_al_tecnico' => true,
                        'finalizado' => false,
                    ],
                ];
            })
        ]);
    }


    /**
     * Asignar trabajo.
     */
    public function asignar(Trabajo $trabajo)
    {
        $hoy = now()->format('Y-m-d');
        $ayer = now()->subDay()->format('Y-m-d');
        $userId = auth()->id();

        $puedeAsignar = Trabajo::where('id', $trabajo->id)
            ->whereDoesntHave('tecnicos', function ($query) use ($userId) {
                $query->where('tecnico_id', $userId);
            })
            ->where(function ($query) use ($hoy, $ayer) {
                $query->whereNull('fecha_salida')
                    ->orWhereDate('fecha_salida', $hoy)
                    ->orWhereDate('fecha_salida', $ayer)
                    ->orWhere('disponible', true);
            })
            ->exists();

        if (!$puedeAsignar) {
            return response()->json([
                'message' => 'No tienes permiso para asignar este trabajo'
            ], 403);
        }

        auth()->user()->trabajos()->attach($trabajo->id, [
            'finalizado' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Trabajo asignado correctamente',
            'trabajo_id' => $trabajo->id
        ]);
    }

    /**
     * Finalizar trabajo asignado.
     */
    public function finalizar(Trabajo $trabajo)
    {
        $user = auth()->user();

        $trabajoAsignado = $user->trabajos()
            ->where('trabajo_id', $trabajo->id)
            ->wherePivot('finalizado', false)
            ->exists();

        if (!$trabajoAsignado) {
            return response()->json([
                'message' => 'No puedes finalizar un trabajo no asignado o ya finalizado'
            ], 403);
        }

        $user->trabajos()->updateExistingPivot($trabajo->id, [
            'finalizado' => true,
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'Trabajo finalizado correctamente'
        ]);
    }

    /**
     * Abandonar un trabajo asignado.
     */
    public function abandonar(Trabajo $trabajo)
    {
        auth()->user()->trabajos()->detach($trabajo->id);

        return response()->json([
            'message' => 'Has abandonado el trabajo'
        ]);
    }
}
