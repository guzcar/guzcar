<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contabilidad extends Trabajo
{
    protected $table = "trabajos";

    public function comprobantes(): BelongsToMany
    {
        return $this->belongsToMany(Comprobante::class, 'trabajo_comprobantes', 'trabajo_id', 'comprobante_id')
            ->withTimestamps();
    }

    public function getImporteNetoAttribute(): float
    {
        if ($this->comprobantes->isEmpty()) {
            return (float) $this->importe;
        }

        return $this->comprobantes->sum(function ($comprobante) {
            return $comprobante->aplica_detraccion
                ? $comprobante->total * 0.88
                : $comprobante->total;
        });
    }
}
