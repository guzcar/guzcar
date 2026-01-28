<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlEquipoDetalle extends Model
{
    use HasFactory;

    protected $table = 'control_equipo_detalles';

    protected $fillable = [
        'control_equipo_id',
        'equipo_detalle_id',
        'implemento_id',
        'estado',
        'observacion',
        'prev_estado',
        'prev_deleted_at',
    ];

    protected $casts = [
        'estado' => 'string',
        'prev_estado' => 'string',
        'prev_deleted_at' => 'datetime',
    ];

    public function control()
    {
        return $this->belongsTo(ControlEquipo::class, 'control_equipo_id');
    }

    public function equipoDetalle()
    {
        return $this->belongsTo(EquipoDetalle::class, 'equipo_detalle_id');
    }

    public function implemento()
    {
        return $this->belongsTo(Implemento::class, 'implemento_id');
    }
}