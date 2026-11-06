<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Abono;

class Pedido extends Model
{
    protected $fillable = [
        'cliente_id',
        'vendedor_id',
        'estado_pedido',
        'estado_pago',
        'fecha_entrega_prometida',
        'notas_produccion',
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

        // Relación con el usuario que registró la venta
    public function vendedor()
    {
        return $table->belongsTo(User::class, 'vendedor_id');
    }

    // Método útil para calcular el saldo pendiente
    public function getSaldoPendienteAttribute()
    {
        $totalAbonado = $this->abonos()->sum('monto');
        return $this->precio_total - $totalAbonado;
    }
}