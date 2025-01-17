<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'costo'
    ];

    public function trabajos(): BelongsToMany
    {
        return $this->belongsToMany(Trabajo::class, 'trabajo_servicios', 'servicio_id', 'trabajo_id');
    }
}
