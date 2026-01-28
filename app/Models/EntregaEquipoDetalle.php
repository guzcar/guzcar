<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntregaEquipoDetalle extends Model
{
    protected $table = 'entrega_equipo_detalles';

    protected $fillable = [
        'entrega_equipo_id',
        'implemento_id',
    ];

    public function entregaEquipo(): BelongsTo
    {
        return $this->belongsTo(EntregaEquipo::class, 'entrega_equipo_id');
    }

    public function implemento(): BelongsTo
    {
        return $this->belongsTo(Implemento::class);
    }
}