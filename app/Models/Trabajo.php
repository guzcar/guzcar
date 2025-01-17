<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trabajo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehiculo_id',
        'taller_id',
        'fecha_ingreso',
        'fecha_salida',
        'descripcion_servicio',
        'desembolso'
    ];

    public function taller()
    {
        return $this->belongsTo(Taller::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class);
    }

    public function mecanicos(): HasMany
    {
        return $this->hasMany(TrabajoMecanico::class, 'trabajo_id');
    }

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trabajo_mecanicos', 'trabajo_id', 'mecanico_id');
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(TrabajoServicio::class, 'trabajo_id');
    }
}
