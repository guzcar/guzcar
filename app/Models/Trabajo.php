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
        'cliente_id',
        'vehiculo_id',
        'conductor_id',
        'taller_id',
        'fecha_ingreso',
        'hora_ingreso',
        'fecha_salida',
        'hora_salida',
        'kilometraje',
        'descripcion_servicio',
        'desembolso',
        'presupuesto_enviado',
        'disponible',
        'garantia',
        'observaciones',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'hora_ingreso' => 'datetime',
        'fecha_salida' => 'date',
        'hora_salida' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function firstCliente()
    {
        if ($this->cliente_id) {
            return $this->cliente;
        }

        return $this->vehiculo?->clientes?->first();
    }

    public function conductor()
    {
        return $this->belongsTo(Cliente::class, 'conductor_id');
    }

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

    public function evidencias_2(): HasMany
    {
        return $this->hasMany(Evidencia::class)->orderBy('mostrar', 'desc');
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

    public function detalles()
    {
        return $this->hasMany(TrabajoDetalle::class);
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

    // public function importe(): float
    // {
    //     // 1. Sumar todos los servicios (precio x cantidad)
    //     $totalServicios = $this->servicios->sum(
    //         fn($servicio) => $servicio->precio * $servicio->cantidad
    //     );

    //     // 2. Sumar artículos con presupuesto activo (precio x cantidad)
    //     $totalArticulos = $this->trabajoArticulos->where('presupuesto', true)->sum(
    //         fn($articulo) => $articulo->precio * $articulo->cantidad
    //     );

    //     // 3. Sumar otros conceptos (precio x cantidad)
    //     $totalOtros = $this->otros->sum(
    //         fn($otro) => $otro->precio * $otro->cantidad
    //     );

    //     // 4. Calcular subtotal antes de descuentos
    //     $subtotal = $totalServicios + $totalArticulos + $totalOtros;

    //     // 5. Aplicar descuentos generales si existen (suma de porcentajes)
    //     if ($this->descuentos->isNotEmpty()) {
    //         $porcentajeDescuento = $this->descuentos->sum('descuento');
    //         $subtotal *= (1 - ($porcentajeDescuento / 100));
    //     }

    //     // 6. Retornar el importe final (nunca negativo)
    //     return $subtotal;
    // }

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
