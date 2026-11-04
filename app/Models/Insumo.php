<?php

 namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;

class Insumo extends Model
{
    protected $fillable = ['nombre', 'stock_actual', 'stock_minimo', 'unidad_medida'];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_insumo')
                    ->withPivot('cantidad_requerida')
                    ->withTimestamps();
    }
}