<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticuloUbicacion extends Pivot
{
    use HasFactory;

    protected $table = 'articulo_ubicaciones';

    protected $fillable = ['articulo_id', 'ubicacion_id'];

    public function articulo()
    {
        return $this->belongsTo(Articulo::class);
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }
}
