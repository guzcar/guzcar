<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoInformePlantilla extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'contenido'
    ];
}
