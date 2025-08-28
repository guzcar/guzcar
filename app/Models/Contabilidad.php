<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contabilidad extends Trabajo
{
    protected $table = "trabajos";

    protected $with = ['comprobantes'];

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

        return (float) $this->comprobantes->sum(function ($comprobante) {
            return $comprobante->aplica_detraccion
                ? $comprobante->total * 0.88
                : $comprobante->total;
        });
    }

    /**
     * Por cobrar correcto: importe_neto - a_cuenta (nunca negativo)
     */
    public function getPorCobrar(): float
    {
        $neto    = (float) $this->importe_neto; // usa el accessor ya calculado
        $aCuenta = (float) $this->a_cuenta;

        return max($neto - $aCuenta, 0);
    }
}
