<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'categoria_id',
        'marca_id',
        'sub_categoria_id',
        'especificacion',
        'presentacion_id',
        'medida',
        'unidad_id',
        'color',
        'descripcion',
        'costo',
        'precio',
        'stock',
        'abiertos',
        'mermas',
        'fraccionable',
    ];

    public function categoria()
    {
        return $this->belongsTo(ArticuloCategoria::class, 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo(ArticuloMarca::class, 'marca_id');
    }

    public function subCategoria()
    {
        return $this->belongsTo(ArticuloSubCategoria::class, 'sub_categoria_id');
    }

    public function unidad()
    {
        return $this->belongsTo(ArticuloUnidad::class, 'unidad_id');
    }

    public function presentacion()
    {
        return $this->belongsTo(ArticuloPresentacion::class, 'presentacion_id');
    }

    public function articuloUbicaciones()
    {
        return $this->hasMany(ArticuloUbicacion::class, 'articulo_id');
    }

    public function ubicaciones()
    {
        return $this->belongsToMany(Ubicacion::class, 'articulo_ubicaciones', 'articulo_id', 'ubicacion_id');
    }

    public function trabajos()
    {
        return $this->belongsToMany(Trabajo::class, 'trabajo_articulos', 'articulo_id', 'trabajo_id')
            ->withPivot(['fecha', 'hora', 'precio', 'cantidad', 'tecnico_id', 'responsable_id', 'movimiento', 'observacion'])
            ->withTimestamps();
    }
}
