<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'especificacion',
        'marca',
        'tamano_presentacion',
        'descripcion',
        'precio',
        'sub_categoria_id',
    ];

    public function subCategoria()
    {
        return $this->belongsTo(SubCategoria::class);
    }

    public function articuloUbicaciones()
    {
        return $this->hasMany(ArticuloUbicacion::class, 'articulo_id');
    }

    public function ubicaciones()
    {
        return $this->belongsToMany(Ubicacion::class, 'articulo_ubicaciones', 'articulo_id', 'ubicacion_id');
    }
}
