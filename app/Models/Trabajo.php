<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trabajo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehiculo_id',
        'taller_id',
        'fecha_ingreso',
        'fecha_salida',
        'descripcion_servicio',
        'desembolso'
    ];

    public function taller()
    {
        return $this->belongsTo(Taller::class)->withTrashed();
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class)->withTrashed();
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class);
    }

    public function tecnicos()
    {
        return $this->hasMany(TrabajoTecnico::class, 'trabajo_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'trabajo_tecnicos', 'trabajo_id', 'tecnico_id')->withTrashed();
    }

    public function servicios(): HasMany
    {
        return $this->hasMany(TrabajoServicio::class, 'trabajo_id');
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(TrabajoArchivo::class, 'trabajo_id', 'id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(TrabajoPago::class, 'trabajo_id', 'id');
    }

    /**
     * Obtiene el importe total sumando precios y cantidades de los servicios.
     *
     * @return float
     */
    public function getImporte(): float
    {
        return round($this->servicios->sum(fn($servicio) => $servicio->precio * $servicio->cantidad), 2);
    }

    /**
     * Obtiene el monto total de los pagos realizados.
     *
     * @return float
     */
    public function getACuenta(): float
    {
        return round($this->pagos->sum('monto'), 2);
    }

    /**
     * Calcula la diferencia entre el importe total y los pagos realizados.
     *
     * @return float
     */
    public function getPorCobrar(): float
    {
        return round($this->getImporte() - $this->getACuenta(), 2);
    }
}
