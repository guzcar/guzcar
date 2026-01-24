<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntregaMaletaDetalle extends Model
{
    protected $table = 'entrega_maleta_detalles';

    protected $fillable = [
        'entrega_maleta_id',
        'herramienta_id',
    ];

    /**
     * La cabecera de la entrega a la que pertenece este detalle.
     */
    public function entregaMaleta(): BelongsTo
    {
        return $this->belongsTo(EntregaMaleta::class, 'entrega_maleta_id');
    }

    /**
     * La herramienta específica.
     */
    public function herramienta(): BelongsTo
    {
        return $this->belongsTo(Herramienta::class);
    }
}