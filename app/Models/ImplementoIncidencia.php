<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImplementoIncidencia extends Model
{
    use HasFactory;

    protected $table = 'implemento_incidencias';

    protected $fillable = [
        'fecha',
        'tipo_origen',
        'equipo_detalle_id', // Antes maleta_detalle_id
        'implemento_id',
        'cantidad',
        'propietario_id',
        'responsable_id',
        'motivo',
        'prev_estado',
        'prev_deleted_at',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'tipo_origen' => 'string',
        'motivo' => 'string',
        'prev_estado' => 'string',
        'prev_deleted_at' => 'datetime',
        'cantidad' => 'integer',
    ];

    public function implemento()
    {
        return $this->belongsTo(Implemento::class, 'implemento_id');
    }

    public function equipoDetalle()
    {
        return $this->belongsTo(EquipoDetalle::class, 'equipo_detalle_id');
    }

    public function propietario()
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}