<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    use HasFactory;

    protected $fillable = [
        'pedido_id',
        'monto',
        'metodo_pago',
        'notas',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}