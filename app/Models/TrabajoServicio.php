<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabajoServicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'sort',
        'trabajo_id',
        'servicio_id',
        'precio'
    ];

    public function servicio(): BelongsTo
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function trabajo(): BelongsTo
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id');
    }
}
