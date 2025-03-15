<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloUnidad extends Model
{
    use HasFactory;

    protected $table = 'articulo_unidades';

    protected $fillable = [
        'nombre'
    ];
}
