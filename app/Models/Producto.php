<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = ['nombre', 'precio_venta'];

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class, 'producto_insumo')
                    ->withPivot('cantidad_requerida')
                    ->withTimestamps();
    }
}