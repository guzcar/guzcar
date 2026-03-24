<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaRepuesto extends Model
{
    use HasFactory;

    protected $table = 'categoria_repuestos';

    protected $fillable = [
        'nombre',
        'color',
    ];

    public function repuestos(): HasMany
    {
        return $this->hasMany(Repuesto::class, 'categoria_id');
    }
}