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

    // Método para calcular el costo total de producción basado en la receta (BOM)
    public function getCostoProduccionAttribute()
    {
        return $this->insumos->sum(function ($insumo) {
            return $insumo->costo_unitario * $insumo->pivot->cantidad_requerida;
        });
    }

}