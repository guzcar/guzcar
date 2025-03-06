<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'fecha',
        'hora',
        'observacion',
        'trabajo_id',
        'tecnico_id',
        'responsable_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
    ];

    public function trabajoArticulos()
    {
        return $this->hasMany(TrabajoArticulo::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id');
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}
