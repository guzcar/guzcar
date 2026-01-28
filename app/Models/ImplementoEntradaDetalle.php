<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImplementoEntradaDetalle extends Model
{
    use HasFactory;

    protected $table = 'implemento_entrada_detalles';

    protected $fillable = [
        'implemento_entrada_id',
        'implemento_id',
        'cantidad',
        'costo',
    ];

    protected $casts = [
        'implemento_entrada_id' => 'integer',
        'implemento_id' => 'integer',
        'cantidad' => 'integer',
        'costo' => 'decimal:2',
    ];

    public function entrada()
    {
        return $this->belongsTo(ImplementoEntrada::class, 'implemento_entrada_id');
    }

    public function implemento()
    {
        return $this->belongsTo(Implemento::class, 'implemento_id');
    }
}