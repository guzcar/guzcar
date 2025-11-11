<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $fillable = [
        'vehiculo_id',
        'cliente_id',
        'igv',
        'observacion'
    ];

    protected $casts = [
        'igv' => 'boolean',
    ];

    /**
     * Relación con el vehículo
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Relación con el cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con los servicios de la cotización
     */
    public function servicios(): HasMany
    {
        return $this->hasMany(CotizacionServicio::class);
    }

    /**
     * Relación con los artículos de la cotización
     */
    public function articulos(): HasMany
    {
        return $this->hasMany(CotizacionArticulo::class);
    }

    /**
     * Calcular subtotal de servicios
     */
    public function getSubtotalServiciosAttribute(): float
    {
        return $this->servicios->sum(function($servicio) {
            return $servicio->cantidad * $servicio->precio;
        });
    }

    /**
     * Calcular subtotal de artículos
     */
    public function getSubtotalArticulosAttribute(): float
    {
        return $this->articulos->sum(function($articulo) {
            return $articulo->cantidad * $articulo->precio;
        });
    }

    /**
     * Calcular subtotal general
     */
    public function getSubtotalAttribute(): float
    {
        return $this->subtotal_servicios + $this->subtotal_articulos;
    }

    /**
     * Calcular IGV
     */
    public function getIgvCalculadoAttribute(): float
    {
        return $this->igv ? $this->subtotal * 0.18 : 0;
    }

    /**
     * Calcular total
     */
    public function getTotalAttribute(): float
    {
        return $this->subtotal + $this->igv_calculado;
    }
}