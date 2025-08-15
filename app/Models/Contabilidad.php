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
}
