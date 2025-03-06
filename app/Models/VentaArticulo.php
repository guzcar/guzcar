<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaArticulo extends Model
{
    protected $fillable = [
        'venta_id',
        'articulo_id',
        'precio',
        'cantidad',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id')->withTrashed();
    }

    public function articulo()
    {
        return $this->belongsTo(Articulo::class, 'articulo_id')->withTrashed();
    }
}
