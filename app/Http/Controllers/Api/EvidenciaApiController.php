<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use App\Models\Trabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenciaApiController extends Controller
{
    /**
     * Listar evidencias de un trabajo (solo del técnico actual)
     */
    public function index(Trabajo $trabajo)
    {
        $evidencias = $trabajo->evidencias()
            ->where('user_id', auth()->id())
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'message' => 'Evidencias obtenidas correctamente',
            'data' => $evidencias->map(function ($e) {
                return [
                    'id' => $e->id,
                    'url' => url('storage/' . $e->evidencia_url),
                    'tipo' => $e->tipo,
                    'observacion' => $e->observacion,
                ];
            })
        ]);
    }


    /**
     * Subir UNA o VARIAS evidencias
     * "files[]" puede venir con 1 o N archivos
     * "observacion" puede venir UNA sola para todas
     */
    public function store(Request $request, Trabajo $trabajo)
    {
        $request->validate([
            'files.*' => 'required|file|max:20480',
            'observacion' => 'nullable|string',
        ]);

        $files = $request->file('files');
        $descripcion = $request->observacion;

        $resultado = [];

        foreach ($files as $file) {
            // 1) Detectar MIME real (más confiable que el nombre)
            $mime = $file->getMimeType(); // ej: image/jpeg, video/mp4

            if (str_starts_with($mime, 'video/')) {
                $tipo = 'video';
            } elseif (str_starts_with($mime, 'image/')) {
                $tipo = 'imagen';
            } else {
                // si vino otra cosa (raro, porque el picker ya filtra)
                $tipo = 'imagen';
            }

            // 2) Extensión "bonita" en base al MIME
            //    usamos extension() que se basa en el MIME y no en el nombre .tmp
            $ext = strtolower($file->extension() ?? '');

            if (!$ext) {
                // fallback según MIME
                if (str_starts_with($mime, 'image/')) {
                    $ext = match ($mime) {
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/webp' => 'webp',
                        default => 'jpg',
                    };
                } elseif (str_starts_with($mime, 'video/')) {
                    $ext = match ($mime) {
                        'video/mp4' => 'mp4',
                        'video/3gpp' => '3gp',
                        'video/quicktime' => 'mov',
                        default => 'mp4',
                    };
                } else {
                    $ext = 'bin';
                }
            }

            // 3) Nombre final con extensión correcta
            $filename = uniqid('evidencia_') . '.' . $ext;

            // 4) Guardar archivo
            $file->storeAs('public/evidencia', $filename);

            // 5) Crear registro
            $e = Evidencia::create([
                'trabajo_id' => $trabajo->id,
                'user_id' => auth()->id(),
                'evidencia_url' => 'evidencia/' . $filename,
                'tipo' => $tipo,
                'observacion' => $descripcion,
            ]);

            $resultado[] = [
                'id' => $e->id,
                'url' => url('storage/evidencia/' . $filename),
                'tipo' => $tipo,
                'observacion' => $descripcion,
            ];
        }

        return response()->json([
            'message' => 'Evidencias registradas correctamente',
            'data' => $resultado,
        ], 201);
    }

    /**
     * Editar evidencia individual (solo observación)
     */
    public function update(Request $request, Evidencia $evidencia)
    {
        if ($evidencia->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'observacion' => 'nullable|string',
        ]);

        $evidencia->update([
            'observacion' => $request->observacion
        ]);

        return response()->json([
            'message' => 'Evidencia actualizada correctamente',
            'data' => [
                'id' => $evidencia->id,
                'url' => url('storage/' . $evidencia->evidencia_url),
                'tipo' => $evidencia->tipo,
                'observacion' => $evidencia->observacion,
            ]
        ]);
    }


    /**
     * BULK UPDATE: cambiar la misma observación a varias evidencias
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'evidencia_ids' => 'required|array',
            'observacion' => 'nullable|string',
        ]);

        Evidencia::whereIn('id', $request->evidencia_ids)
            ->where('user_id', auth()->id())
            ->update([
                'observacion' => $request->observacion
            ]);

        return response()->json([
            'message' => 'Evidencias actualizadas correctamente'
        ]);
    }


    /**
     * Eliminar evidencia individual
     */
    public function destroy(Evidencia $evidencia)
    {
        if ($evidencia->user_id !== auth()->id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $evidencia->delete();

        return response()->json([
            'message' => 'Evidencia eliminada correctamente'
        ]);
    }


    /**
     * BULK DELETE: eliminar varias evidencias
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'evidencia_ids' => 'required|array',
        ]);

        Evidencia::whereIn('id', $request->evidencia_ids)
            ->where('user_id', auth()->id())
            ->delete();

        return response()->json([
            'message' => 'Evidencias eliminadas correctamente'
        ]);
    }
}
