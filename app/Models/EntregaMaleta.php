<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EntregaMaleta extends Model
{
    protected $table = 'entrega_maletas';

    protected $fillable = [
        'maleta_id',
        'propietario_id',
        'responsable_id',
        'evidencia',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /**
     * La maleta que se entrega.
     */
    public function maleta(): BelongsTo
    {
        return $this->belongsTo(Maleta::class);
    }

    /**
     * El dueño original de la maleta al momento de la entrega.
     */
    public function propietario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'propietario_id');
    }

    /**
     * La persona responsable de recibir la entrega.
     */
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Los detalles (herramientas) incluidas en esta entrega.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(EntregaMaletaDetalle::class, 'entrega_maleta_id');
    }
}