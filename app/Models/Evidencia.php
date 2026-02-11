<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Evidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'trabajo_id',
        'user_id',
        'evidencia_url',
        'tipo',
        'observacion',
        'sort',
        'mostrar',
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    protected static function booted(): void
    {
        // Evento para cuando se elimina una evidencia
        static::deleting(function (self $evidencia) {
            // Verificar y eliminar el archivo de evidencia antes de eliminar el modelo
            if ($evidencia->evidencia_url) {
                $filePath = 'public/evidencia/' . basename($evidencia->evidencia_url);
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
                    $originalFilePath = 'public/evidencia/' . basename($originalEvidenciaUrl);

                    // Verificar si el archivo existe y eliminarlo
                    if (Storage::exists($originalFilePath)) {
                        Storage::delete($originalFilePath);
                    }
                }
            }
        });
    }

    public function getThumbnailBase64Attribute()
    {
        // 1. Limpiamos la ruta
        $path = public_path('storage/' . str_replace('public/', '', $this->evidencia_url));

        // 2. Si no existe la imagen, retornamos vacío o una imagen placeholder
        if (!file_exists($path)) {
            return "";
        }

        try {
            // 3. Obtenemos información de la imagen original
            list($width, $height, $type) = getimagesize($path);

            // 4. Definimos el nuevo ancho (400px es suficiente para el PDF)
            $newWidth = 400;
            $ratio = $width / $height;
            $newHeight = $newWidth / $ratio;

            // 5. Creamos el lienzo para la nueva imagen pequeña
            $src = null;
            $dst = imagecreatetruecolor($newWidth, $newHeight);

            // Cargamos la imagen según su tipo
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $src = imagecreatefromjpeg($path);
                    break;
                case IMAGETYPE_PNG:
                    $src = imagecreatefrompng($path);
                    // Mantenemos transparencia básica si es PNG
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                    break;
                case IMAGETYPE_GIF:
                    $src = imagecreatefromgif($path);
                    break;
                default:
                    return ""; // Tipo no soportado
            }

            if (!$src)
                return "";

            // 6. Redimensionamos (Aquí ocurre la magia de bajar el peso)
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // 7. Capturamos la salida en un buffer en lugar de guardarla en disco
            ob_start();

            // Convertimos todo a JPEG con calidad 60 (muy ligero)
            // Aunque la original sea PNG, para PDF es mejor JPG porque pesa menos
            imagejpeg($dst, null, 60);

            $data = ob_get_clean();

            // Liberamos memoria
            imagedestroy($src);
            imagedestroy($dst);

            // 8. Retornamos el string base64 listo para poner en el src=""
            return 'data:image/jpeg;base64,' . base64_encode($data);

        } catch (\Exception $e) {
            // Si algo falla, retornamos vacío para no romper el PDF
            return "";
        }
    }
}
