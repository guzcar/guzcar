<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $fillable = [
        'codigo',
        'fecha',
        'hora',
        'observacion',
        'responsable_id',
        'cliente_id',
        'vehiculo_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id')->withTrashed();
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id')->withTrashed();
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id')->withTrashed();
    }

    public function ventaArticulos()
    {
        return $this->hasMany(VentaArticulo::class, 'venta_id');
    }
}

