<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Entrada extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guia',
        'fecha',
        'observacion',
        'responsable_id',
        'evidencia_url',
    ];

    public function articulos()
    {
        return $this->belongsToMany(Articulo::class, 'entrada_articulos', 'entrada_id', 'articulo_id')->withTrashed();
    }

    public function entradaArticulos()
    {
        return $this->hasMany(EntradaArticulo::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    protected static function booted(): void
    {
        // Evento para cuando se elimina permanentemente una entrada
        static::forceDeleting(function (self $entrada) {
            // Verificar y eliminar el archivo de evidencia solo si se está eliminando permanentemente
            if ($entrada->evidencia_url) {
                $filePath = 'public/entrada/' . basename($entrada->evidencia_url);
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath);
                }
            }
        });

        // Evento para cuando se actualiza el modelo (como la URL de evidencia)
        static::updating(function (self $entrada) {
            // Verificar si el atributo evidencia_url ha cambiado
            if ($entrada->isDirty('evidencia_url')) {
                // Obtener la URL anterior de evidencia
                $originalEvidenciaUrl = $entrada->getOriginal('evidencia_url');

                // Si existe una URL anterior, construir la ruta del archivo y eliminarlo
                if ($originalEvidenciaUrl) {
                    $originalFilePath = 'public/entrada/' . basename($originalEvidenciaUrl);

                    // Verificar si el archivo existe y eliminarlo
                    if (Storage::exists($originalFilePath)) {
                        Storage::delete($originalFilePath);
                    }
                }
            }
        });
    }
}
