<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoInforme extends Model
{
    use HasFactory;

    protected $table = 'trabajo_informes';

    protected $fillable = [
        'trabajo_id',
        'contenido',
        'visible',
    ];

    protected $casts = [
        'visible' => 'boolean',
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class);
    }
}
