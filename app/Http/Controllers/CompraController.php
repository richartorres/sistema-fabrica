<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function store(Request $request)
    {
        // Validar los datos de la compra y sus detalles
        $request->validate([
            'proveedor_id' => 'required|exists:proveedors,id',
            'abono' => 'required|numeric|min:0',
            'fecha_compra' => 'required|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.descripcion_material' => 'required|string',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        try {
            // Usamos una transacción de base de datos para garantizar consistencia total
            return DB::transaction(function () use ($request) {
                
                // 1. Calcular el total sumando los subtotales de los materiales
                $totalCompra = 0;
                foreach ($request->detalles as $detalle) {
                    $totalCompra += $detalle['cantidad'] * $detalle['precio_unitario'];
                }

                $abono = $request->abono;
                $saldoPendiente = $totalCompra - $abono;
                
                // Definir estado según el pago
                $estado = ($abono >= $totalCompra) ? 'completado' : 'pendiente';

                // 2. Registrar la cabecera de la compra
                $compra = Compra::create([
                    'proveedor_id' => $request->proveedor_id,
                    'total' => $totalCompra,
                    'abono' => $abono,
                    'saldo_pendiente' => max(0, $saldoPendiente),
                    'estado' => $estado,
                    'fecha_compra' => $request->fecha_compra,
                ]);

                // 3. Registrar cada detalle de material (telas, espuma, herrajes, etc.)
                foreach ($request->detalles as $detalle) {
                    $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
                    
                    DetalleCompra::create([
                        'compra_id' => $compra->id,
                        'descripcion_material' => $detalle['descripcion_material'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                        'subtotal' => $subtotal,
                    ]);
                }

                return response()->json([
                    'message' => 'Compra y detalles de insumos registrados correctamente',
                    'data' => $compra->load('detalles', 'proveedor')
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo procesar la compra',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function registrarAbono(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
        ]);

        try {
            return DB::transaction(function () use ($request, $id) {
                $compra = Compra::findOrFail($id);

                // Validar que el abono no supere el saldo pendiente actual
                if ($request->monto > $compra->saldo_pendiente) {
                    return response()->json([
                        'error' => 'El monto del abono supera el saldo pendiente de la factura.'
                    ], 400);
                }

                // Actualizar abono y saldo pendiente
                $compra->abono += $request->monto;
                $compra->saldo_pendiente -= $request->monto;

                // Actualizar estado si el saldo llega a 0
                if ($compra->saldo_pendiente <= 0) {
                    $compra->saldo_pendiente = 0;
                    $compra->estado = 'completado';
                } else {
                    $compra->estado = 'pendiente';
                }

                $compra->save();

                return response()->json([
                    'message' => 'Abono registrado exitosamente',
                    'data' => $compra->load('detalles', 'proveedor')
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo registrar el abono',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
        public function index()
    {
        $compras = Compra::with(['proveedor', 'detalles'])->orderBy('created_at', 'desc')->get();
        return response()->json($compras, 200);
    }

    public function pendientes()
    {
        $comprasPendientes = Compra::with(['proveedor', 'detalles'])
            ->where('saldo_pendiente', '>', 0)
            ->orderBy('fecha_compra', 'asc')
            ->get();

        return response()->json([
            'total_facturas_pendientes' => $comprasPendientes->count(),
            'cartera_total_pendiente' => $comprasPendientes->sum('saldo_pendiente'),
            'data' => $comprasPendientes
        ], 200);
    }


}

