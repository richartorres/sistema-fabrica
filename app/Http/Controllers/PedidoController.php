<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    // Registrar un nuevo pedido
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $precioTotal = $request->cantidad * $request->precio_unitario;

        $pedido = Pedido::create([
            'cliente_id' => $request->cliente_id,
            'producto_id' => $request->producto_id,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'precio_total' => $precioTotal,
            'estado' => 'pendiente'
        ]);

        return response()->json([
            'message' => 'Pedido registrado exitosamente',
            'pedido' => $pedido
        ], 201);
    }

    // Cambiar el estado del pedido (pendiente -> en_fabricacion -> entregado -> pagado)
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,en_fabricacion,entregado,pagado'
        ]);

        $pedido = Pedido::findOrFail($id);
        $pedido->estado = $request->estado;
        $pedido->save();

        return response()->json([
            'message' => 'Estado del pedido actualizado correctamente',
            'pedido' => $pedido
        ]);
    }
}