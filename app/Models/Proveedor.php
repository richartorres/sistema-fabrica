<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedors';

    protected $fillable = [
        'nit',
        'nombre',
        'telefono',
        'direccion',
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }
}