<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoDetalle extends Model
{
    use HasFactory;

    protected $table = 'trabajo_detalles';

    protected $fillable = [
        'trabajo_id',
        'descripcion',
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class);
    }
}
