<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\OrdenProduccion;
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

                // 3. Calcular costos de producción
                $costoUnitario = $producto->costo_produccion;
                $costoTotal = $costoUnitario * $cantidadProducida;

                // 4. Registrar la Orden de Producción histórica
                $orden = OrdenProduccion::create([
                    'producto_id' => $producto->id,
                    'cantidad_producida' => $cantidadProducida,
                    'costo_unitario_produccion' => $costoUnitario,
                    'costo_total_produccion' => $costoTotal,
                ]);

                return response()->json([
                    'message' => 'Producción registrada, stock descontado y orden generada exitosamente',
                    'producto' => $producto->nombre,
                    'cantidad_fabricada' => $cantidadProducida,
                    'costo_unitario' => $costoUnitario,
                    'costo_total_produccion' => $costoTotal,
                    'orden_id' => $orden->id
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