<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presupuesto extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehiculo_id',
        'cliente_id',
        'igv',
        'observacion',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'igv' => 'boolean',
    ];

    /**
     * Obtiene el vehículo asociado al presupuesto.
     */
    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /**
     * Obtiene el cliente asociado al presupuesto.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Obtiene los servicios del presupuesto.
     */
    public function servicios(): HasMany
    {
        return $this->hasMany(PresupuestoServicio::class);
    }

    /**
     * Obtiene los artículos del presupuesto.
     */
    public function articulos(): HasMany
    {
        return $this->hasMany(PresupuestoArticulo::class);
    }
}