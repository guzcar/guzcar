<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'trabajo_id',
        'evidencia_url',
        'observacion'
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class);
    }
}
