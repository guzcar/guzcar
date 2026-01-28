<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $table = 'equipos';

    protected $fillable = [
        'codigo',
        'propietario_id',
        'evidencia',
        'observacion',
    ];

    public function propietario()
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }

    public function detalles()
    {
        return $this->hasMany(EquipoDetalle::class, 'equipo_id');
    }

    public function controles()
    {
        return $this->hasMany(ControlEquipo::class, 'equipo_id');
    }
}