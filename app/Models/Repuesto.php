<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Repuesto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'cantidad',
        'nombre',
        'categoria_id',
        'marca_modelo',
        'motor',
        'medidas_cod_oem',
        'estado',
        'notas',
        'fecha',
        'tecnico_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'integer',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaRepuesto::class, 'categoria_id');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }
}