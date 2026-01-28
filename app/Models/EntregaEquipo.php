<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntregaEquipo extends Model
{
    protected $table = 'entrega_equipos';

    protected $fillable = [
        'equipo_id',
        'propietario_id',
        'responsable_id',
        'evidencia',
        'fecha',
    ];

    protected $casts = [
        'evidencia' => 'array',
        'fecha' => 'datetime',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(EntregaEquipoDetalle::class, 'entrega_equipo_id');
    }
}