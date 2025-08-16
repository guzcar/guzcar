<?php

namespace App\Models;

use App\Services\TrabajoService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabajoServicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'trabajo_id',
        'servicio_id',
        'detalle',
        'precio',
        'cantidad',
        'sort',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id')->withTrashed();
    }

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id')->withTrashed();
    }

    protected static function booted()
    {
        static::saved(function ($trabajoServicio) {
            $trabajo = $trabajoServicio->trabajo;
            TrabajoService::actualizarTrabajoPorId($trabajo);
        });

        static::deleted(function ($trabajoServicio) {
            $trabajo = $trabajoServicio->trabajo;
            TrabajoService::actualizarTrabajoPorId($trabajo);
        });
    }
}
