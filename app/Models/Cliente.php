<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'identificador',
        'nombre',
        'telefono',
        'direccion',
    ];

    public function vehiculos(): BelongsToMany
    {
        return $this->belongsToMany(Vehiculo::class, 'cliente_vehiculos', 'cliente_id', 'vehiculo_id');
    }
    
    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }
}
