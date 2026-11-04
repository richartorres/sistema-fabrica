<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduccionController extends Controller
{
    public function registrarProduccion(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad_producida' => 'required|integer|min:1',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $producto = Producto::with('insumos')->findOrFail($request->producto_id);
                $cantidadProducida = $request->cantidad_producida;

                // 1. Verificar si hay suficiente stock de todos los insumos necesarios
                foreach ($producto->insumos as $insumo) {
                    $totalRequerido = $insumo->pivot->cantidad_requerida * $cantidadProducida;

                    if ($insumo->stock_actual < $totalRequerido) {
                        throw new \Exception("Stock insuficiente para el insumo: {$insumo->nombre}. Stock actual: {$insumo->stock_actual}, Requerido: {$totalRequerido}");
                    }
                }

                // 2. Descontar automáticamente el stock de cada insumo
                foreach ($producto->insumos as $insumo) {
                    $totalRequerido = $insumo->pivot->cantidad_requerida * $cantidadProducida;
                    
                    $insumo->stock_actual -= $totalRequerido;
                    $insumo->save();
                }

                return response()->json([
                    'message' => 'Producción registrada y stock descontado exitosamente',
                    'producto' => $producto->nombre,
                    'cantidad_fabricada' => $cantidadProducida
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo procesar la producción',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}