<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionArticulo extends Model
{
    use HasFactory;

    protected $table = 'cotizacion_articulos';

    protected $fillable = [
        'cotizacion_id',
        'descripcion',
        'cantidad',
        'precio'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    /**
     * Relación con la cotización
     */
    public function cotizacion(): BelongsTo
    {
        return $this->belongsTo(Cotizacion::class);
    }

    /**
     * Calcular total del artículo
     */
    public function getTotalAttribute(): float
    {
        return $this->cantidad * $this->precio;
    }
}