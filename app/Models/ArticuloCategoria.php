<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ArticuloCategoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['nombre'];

    public function subCategorias()
    {
        return $this->hasMany(ArticuloSubCategoria::class, 'categoria_id');
    }
}
