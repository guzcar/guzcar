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
        'control',
        'codigo',
        'vehiculo_id',
        'taller_id',
        'fecha_ingreso',
        'fecha_salida',
        'descripcion_servicio',
        'desembolso',
        'disponible'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_salida' => 'date',
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
        return $this->belongsToMany(User::class, 'trabajo_tecnicos', 'trabajo_id', 'tecnico_id')
            ->withTrashed();
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

    public function trabajoArticulos()
    {
        return $this->hasMany(TrabajoArticulo::class, 'trabajo_id');
    }

    public function articulos()
    {
        return $this->belongsToMany(Articulo::class, 'trabajo_articulos', 'trabajo_id', 'articulo_id')
            ->withPivot(['fecha', 'hora', 'precio', 'cantidad', 'tecnico_id', 'responsable_id', 'movimiento', 'observacion'])
            ->withTimestamps();
    }

    public function otros()
    {
        return $this->hasMany(TrabajoOtro::class, 'trabajo_id');
    }

    public function descuentos(): HasMany
    {
        return $this->hasMany(TrabajoDescuento::class, 'trabajo_id');
    }

    public function importe(): float
    {
        $total = 0;

        // Sumar servicios (precio * cantidad)
        $total += $this->servicios->sum(function ($servicio) {
            return $servicio->precio * $servicio->cantidad;
        });

        // Sumar artículos con presupuesto=true (precio * cantidad)
        $total += $this->trabajoArticulos->where('presupuesto', true)->sum(function ($articulo) {
            return $articulo->precio * $articulo->cantidad;
        });

        // Sumar otros conceptos (precio * cantidad)
        $total += $this->otros->sum(function ($otro) {
            return $otro->precio * $otro->cantidad;
        });

        return (float) $total;
    }

    /**
     * Calcula la diferencia entre el importe total y los pagos realizados.
     *
     * @return float
     */
    public function getPorCobrar(): float
    {
        // Obtener los valores de importe y a_cuenta (y convertirlos a float)
        $importe = (float) $this->importe;
        $aCuenta = (float) $this->a_cuenta;

        // Calcular la diferencia
        $porCobrar = $importe - $aCuenta;

        // Devolver 0 si el resultado es negativo
        return max($porCobrar, 0);
    }
}
