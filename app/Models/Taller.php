<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Taller extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'talleres';

    protected $fillable = [
        'nombre',
        'ubicacion'
    ];

    public function trabajos()
    {
        return $this->hasMany(Trabajo::class);
    }
}
