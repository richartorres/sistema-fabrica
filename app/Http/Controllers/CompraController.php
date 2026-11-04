<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function store(Request $request)
    {
        // Lógica para registrar la compra y calcular saldos
        $compra = Compra::create([
            'proveedor_id' => $request->proveedor_id,
            'total' => $request->total,
            'abono' => $request->abono,
            'saldo_pendiente' => $request->total - $request->abono,
            'estado' => ($request->abono >= $request->total) ? 'completado' : 'pendiente',
            'fecha_compra' => now(),
        ]);

        return response()->json(['message' => 'Compra registrada con éxito', 'data' => $compra], 201);
    }
}