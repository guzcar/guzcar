<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoDescuento extends Model
{
    use HasFactory;

    protected $fillable = [
        'trabajo_id',
        'detalle',
        'descuento',
    ];

    // Agrega esto dentro de la clase TrabajoDescuento
    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id');
    }
}
