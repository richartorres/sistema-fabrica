<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Abono;

class Pedido extends Model
{
    protected $fillable = [
        'cliente_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'precio_total',
        'estado'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

        public function abonos()
    {
        return $this->hasMany(Abono::class);
    }

    // Método útil para calcular el saldo pendiente
    public function getSaldoPendienteAttribute()
    {
        $totalAbonado = $this->abonos()->sum('monto');
        return $this->precio_total - $totalAbonado;
    }
}