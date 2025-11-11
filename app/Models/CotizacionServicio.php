<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CotizacionServicio extends Model
{
    use HasFactory;

    protected $table = 'cotizacion_servicios';

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
     * Calcular total del servicio
     */
    public function getTotalAttribute(): float
    {
        return $this->cantidad * $this->precio;
    }
}