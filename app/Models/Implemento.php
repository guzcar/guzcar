<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Implemento extends Model
{
    use HasFactory;

    protected $table = 'implementos';

    protected $fillable = [
        'nombre',
        'costo',
        'stock',
        'asignadas',
        'mermas',
        'perdidas',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'stock' => 'integer',
        'asignadas' => 'integer',
        'mermas' => 'integer',
        'perdidas' => 'integer',
    ];

    public function equipoDetalles()
    {
        return $this->hasMany(EquipoDetalle::class, 'implemento_id');
    }

    public function entradaDetalles()
    {
        return $this->hasMany(ImplementoEntradaDetalle::class, 'implemento_id');
    }

    public function controlDetalles()
    {
        return $this->hasMany(ControlEquipoDetalle::class, 'implemento_id');
    }
}