<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Insumo;
use Illuminate\Http\JsonResponse;

class ReporteController extends Controller
{
    // 1. Cálculo de Utilidades y Rentabilidad
    public function rentabilidad(): JsonResponse
    {
        // Total ingresado por pedidos entregados o pagados
        $ingresosTotales = Pedido::whereIn('estado', ['entregado', 'pagado'])->sum('precio_total');

        // Gasto total estimado en producción basado en las órdenes de producción o insumos consumidos
        // (Sumamos los precios de compra de los insumos según su stock actual o histórico)
       $costoInsumosInventario = Insumo::all()->sum(function ($insumo) {
            return $insumo->stock_actual * $insumo->costo_unitario;
        });

        // Ganancia neta estimada preliminar
        $gananciaNeta = $ingresosTotales - $costoInsumosInventario;

        return response()->json([
            'modulo' => 'Reporte Gerencial de Rentabilidad',
            'ingresos_totales_ventas' => $ingresosTotales,
            'valor_actual_insumos_inventario' => $costoInsumosInventario,
            'balance_preliminar' => $gananciaNeta
        ]);
    }

    // 2. Alertas de Stock Bajo
    public function alertasStockBajo(): JsonResponse
    {
        // Definimos un umbral de alerta (por ejemplo, insumos con 10 o menos unidades)
        $umbralCritico = 10;

        $insumosBajos = Insumo::where('stock_actual', '<=', $umbralCritico)->get();

        if ($insumosBajos->isEmpty()) {
            return response()->json([
                'alerta' => 'Normal',
                'message' => 'Los inventarios de insumos están en niveles óptimos. No hay riesgo de desabastecimiento.'
            ]);
        }

        return response()->json([
            'alerta' => '¡Atención!',
            'message' => 'Hay insumos por debajo del stock mínimo recomendado.',
            'insumos_criticos' => $insumosBajos
        ]);
    }
}