<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TrabajoArchivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'archivo_url',
        'trabajo_id',
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id', 'id');
    }

    protected static function booted(): void
    {
        // Evento para cuando se elimina una evidencia
        static::deleting(function (self $evidencia) {
            // Verificar y eliminar el archivo de evidencia antes de eliminar el modelo
            if ($evidencia->evidencia_url) {
                $filePath = 'public/trabajo_archivo/' . basename($evidencia->evidencia_url);
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }
        });

        // Evento para cuando se actualiza el modelo (como la URL de evidencia)
        static::updating(function (self $evidencia) {
            // Verificar si el atributo evidencia_url ha cambiado
            if ($evidencia->isDirty('evidencia_url')) {
                // Obtener la URL anterior de evidencia
                $originalEvidenciaUrl = $evidencia->getOriginal('evidencia_url');

                // Si existe una URL anterior, construir la ruta del archivo y eliminarlo
                if ($originalEvidenciaUrl) {
                    $originalFilePath = 'public/trabajo_archivo/' . basename($originalEvidenciaUrl);

                    // Verificar si el archivo existe y eliminarlo
                    if (Storage::exists($originalFilePath)) {
                        Storage::delete($originalFilePath);
                    }
                }
            }
        });
    }
}
