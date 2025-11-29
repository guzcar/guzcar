<?php

// app/Models/Cronograma.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cronograma extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tarea_id',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(CronogramaTarea::class, 'tarea_id');
    }
}