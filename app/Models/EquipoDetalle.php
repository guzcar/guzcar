<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipoDetalle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipo_detalles';

    protected $fillable = [
        'equipo_id',
        'implemento_id',
        'ultimo_estado',
        'evidencia_url',
    ];

    protected $casts = [
        'equipo_id' => 'integer',
        'ultimo_estado' => 'string',
        'deleted_at' => 'datetime',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function implemento()
    {
        return $this->belongsTo(Implemento::class, 'implemento_id');
    }

    public function controlDetalles()
    {
        return $this->hasMany(ControlEquipoDetalle::class, 'equipo_detalle_id');
    }

    public function incidencias()
    {
        return $this->hasMany(ImplementoIncidencia::class, 'equipo_detalle_id');
    }
}