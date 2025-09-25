<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoVehiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'diagrama',
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'tipo_vehiculo_id');
    }
}
