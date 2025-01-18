<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabajoPago extends Model
{
    use HasFactory;

    protected $fillable = [
        'trabajo_id',
        'monto',
        'fecha_pago',
        'observacion',
        'detalle_id',
    ];

    public function detalle(): BelongsTo
    {
        return $this->belongsTo(TrabajoPagoDetalle::class, 'detalle_id');
    }

    public function trabajo(): BelongsTo
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id', 'id');
    }
}
