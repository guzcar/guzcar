<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntradaArticulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrada_id',
        'articulo_id',
        'costo',
        'cantidad'
    ];

    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'entrada_id')->withTrashed();
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id')->withTrashed();
    }
}
