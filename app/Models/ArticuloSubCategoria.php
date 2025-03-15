<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticuloSubCategoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'categoria_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(ArticuloCategoria::class);
    }

    public function articulos()
    {
        return $this->hasMany(Articulo::class);
    }
}
