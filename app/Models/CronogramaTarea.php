<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CronogramaTarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    public function cronogramas(): HasMany
    {
        return $this->hasMany(Cronograma::class, 'tarea_id');
    }
}