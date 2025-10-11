<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloGrupo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'color'
    ];

    public function articulos()
    {
        return $this->hasMany(Articulo::class, 'grupo_id');
    }
}
