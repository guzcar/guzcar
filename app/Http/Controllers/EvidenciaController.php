<?php

namespace App\Http\Controllers;

use App\Models\Trabajo;
use App\Models\Evidencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenciaController extends Controller
{
    /**
     * Mostrar todas las evidencias del trabajo actual que pertenecen al usuario.
     * 
     * @param \App\Models\Trabajo $trabajo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios->contains($user) || $trabajo->fecha_salida !== null) {
            abort(403, 'Forbidden');
        }

        $evidencias = Evidencia::where('trabajo_id', $trabajo->id)
            ->where('user_id', $user->id)
            ->get();

        return view('evidencias.index', compact('evidencias', 'trabajo'));
    }

    /**
     * Subir una nueva evidencia.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Trabajo $trabajo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, Trabajo $trabajo)
    {
        $user = auth()->user();

        if (!$trabajo->usuarios()->where('tecnico_id', $user->id)->exists()) {
            abort(403, 'Forbidden');
        }

        $request->validate([
            'evidencia' => 'required|file|mimes:jpg,jpeg,png,mp4,mov',
            'observacion' => 'nullable|string',
        ]);

        $file = $request->file('evidencia');
        $path = $file->store('evidencia', 'public');

        // Optimizar imágenes con GD
        if (in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
            $this->optimizeImage(Storage::disk('public')->path($path), $file->getMimeType());
        }

        Evidencia::create([
            'trabajo_id' => $trabajo->id,
            'user_id' => $user->id,
            'evidencia_url' => $path,
            'tipo' => $file->getMimeType() === 'video/mp4' ? 'video' : 'imagen',
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('evidencias.index', $trabajo)->with('success', 'Evidencia subida correctamente.');
    }

    /**
     * Actualizar una evidencia existente.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Trabajo $trabajo
     * @param \App\Models\Evidencia $evidencia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Trabajo $trabajo, Evidencia $evidencia)
    {
        $user = auth()->user();

        if ($evidencia->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        $request->validate([
            'observacion' => 'nullable|string',
            'evidencia' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:10240',
        ]);

        if ($request->hasFile('evidencia')) {
            Storage::disk('public')->delete($evidencia->evidencia_url);
            $file = $request->file('evidencia');
            $path = $file->store('evidencia', 'public');

            // Optimizar imágenes con GD
            if (in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
                $this->optimizeImage(Storage::disk('public')->path($path), $file->getMimeType());
            }

            $evidencia->evidencia_url = $path;
            $evidencia->tipo = $file->getMimeType() === 'video/mp4' ? 'video' : 'imagen';
        }

        $evidencia->observacion = $request->observacion;
        $evidencia->save();

        return redirect()->route('evidencias.index', $trabajo)->with('success', 'Evidencia actualizada correctamente.');
    }

    /**
     * Eliminar una evidencia.
     * 
     * @param \App\Models\Trabajo $trabajo
     * @param \App\Models\Evidencia $evidencia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Trabajo $trabajo, Evidencia $evidencia)
    {
        $user = auth()->user();

        if ($evidencia->user_id !== $user->id) {
            abort(403, 'Forbidden');
        }

        Storage::disk('public')->delete($evidencia->evidencia_url);
        $evidencia->delete();

        return redirect()->route('evidencias.index', $trabajo)->with('success', 'Evidencia eliminada correctamente.');
    }

    /**
     * Optimizar imagen utilizando GD.
     * 
     * @param mixed $path
     * @param mixed $mimeType
     * @return void
     */
    private function optimizeImage($path, $mimeType)
    {
        $maxWidth = 1920;

        // Crear la imagen a partir del archivo
        if ($mimeType === 'image/jpeg') {
            $image = imagecreatefromjpeg($path);
        } elseif ($mimeType === 'image/png') {
            $image = imagecreatefrompng($path);
        } else {
            return; // Si no es JPEG o PNG, no hacer nada
        }

        // Obtener dimensiones originales
        $width = imagesx($image);
        $height = imagesy($image);

        // Calcular nuevas dimensiones manteniendo la proporción
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Redimensionar la imagen
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Guardar la imagen optimizada
        if ($mimeType === 'image/jpeg') {
            imagejpeg($resizedImage, $path, 85); // Calidad 85%
        } elseif ($mimeType === 'image/png') {
            imagepng($resizedImage, $path, 8); // Nivel de compresión 8
        }

        // Liberar memoria
        imagedestroy($image);
        imagedestroy($resizedImage);
    }
}
