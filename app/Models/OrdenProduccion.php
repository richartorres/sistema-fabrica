<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenProduccion extends Model
{
    protected $fillable = [
        'producto_id',
        'cantidad_producida',
        'costo_unitario_produccion',
        'costo_total_produccion'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
