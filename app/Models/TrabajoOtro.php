<?php

namespace App\Models;

use App\Services\TrabajoService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoOtro extends Model
{
    use HasFactory;

    protected $fillable = [
        'descripcion',
        'precio',
        'cantidad',
        'sort',
        'presupuesto',
    ];

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
