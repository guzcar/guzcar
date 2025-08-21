<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrabajoDescripcionTecnico extends Model
{
    use HasFactory;

    protected $fillable = [
        'trabajo_id',
        'user_id',
        'descripcion'
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
