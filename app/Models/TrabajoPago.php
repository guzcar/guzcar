<?php

namespace App\Models;

use App\Services\TrabajoService;
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

    protected $casts = [
        'fecha_pago' => 'datetime',
    ];

    public function detalle(): BelongsTo
    {
        return $this->belongsTo(TrabajoPagoDetalle::class, 'detalle_id');
    }

    public function trabajo(): BelongsTo
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id', 'id');
    }

    protected static function booted()
    {
        static::saved(function ($trabajoPago) {
            $trabajo = $trabajoPago->trabajo;
            TrabajoService::actualizarTrabajoPorId($trabajo);
        });

        static::deleted(function ($trabajoPago) {
            $trabajo = $trabajoPago->trabajo;
            TrabajoService::actualizarTrabajoPorId($trabajo);
        });
    }
}
