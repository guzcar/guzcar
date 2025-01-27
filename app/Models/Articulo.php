<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
