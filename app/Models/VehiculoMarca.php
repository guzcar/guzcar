<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehiculoMarca extends Model
{
    use HasFactory;

    protected $fillable = ['nombre'];

    public function modelos()
    {
        return $this->hasMany(VehiculoModelo::class, 'marca_id');
    }
}
