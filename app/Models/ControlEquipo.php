<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlEquipo extends Model
{
    use HasFactory;

    protected $table = 'control_equipos';

    protected $fillable = [
        'equipo_id',
        'fecha',
        'responsable_id',
        'propietario_id',
        'evidencia_url',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function propietario()
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }

    public function detalles()
    {
        return $this->hasMany(ControlEquipoDetalle::class, 'control_equipo_id');
    }
}