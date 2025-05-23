<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'placa',
        'marca_id',
        'modelo_id',
        'color',
        'vin',
        'motor',
        'ano',
        'tipo_vehiculo_id',
    ];

    public function marca()
    {
        return $this->belongsTo(VehiculoMarca::class, 'marca_id');
    }

    public function modelo()
    {
        return $this->belongsTo(VehiculoModelo::class, 'modelo_id');
    }

    public function tipoVehiculo(): BelongsTo
    {
        return $this->belongsTo(TipoVehiculo::class, 'tipo_vehiculo_id');
    }

    public function propietarios(): HasMany
    {
        return $this->hasMany(ClienteVehiculo::class, 'vehiculo_id');
    }

    public function clientes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'cliente_vehiculos', 'vehiculo_id', 'cliente_id');
    }

    public function trabajos()
    {
        return $this->hasMany(Trabajo::class);
    }
}
