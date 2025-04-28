<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiculoModelo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'marca_id'];

    public function marca()
    {
        return $this->belongsTo(VehiculoMarca::class, 'marca_id');
    }
}
