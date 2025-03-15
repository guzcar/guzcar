<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloPresentacion extends Model
{
    use HasFactory;

    protected $table = 'articulo_presentaciones';

    protected $fillable = [
        'nombre'
    ];
}
