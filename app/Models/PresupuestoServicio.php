<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresupuestoServicio extends Model
{
    use HasFactory;

    /**
     * El nombre de la tabla asociada con el modelo.
     * (Opcional si Laravel lo infiere correctamente, pero es buena práctica)
     * @var string
     */
    protected $table = 'presupuesto_servicios';

    /**
     * Los atributos que se pueden asignar masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'presupuesto_id',
        'descripcion',
        'cantidad',
        'precio',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
    ];

    /**
     * Obtiene el presupuesto al que pertenece este servicio.
     */
    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class);
    }
}