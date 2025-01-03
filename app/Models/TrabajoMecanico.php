<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabajoMecanico extends Model
{
    use HasFactory;

    protected $fillable = [
        'trabajo_id',
        'mecanico_id',
    ];

    public function trabajo(): BelongsTo
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id');
    }

    public function mecanico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mecanico_id');
    }
}
