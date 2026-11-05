<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PedidoPdfController extends Controller
{
    public function descargar(Pedido $pedido)
    {
        // Cargar la relación con cliente si existe
        $pedido->load('cliente');

        // Cargar la vista Blade y pasar los datos del pedido
        $pdf = Pdf::loadView('pdf.factura', compact('pedido'));

        // Retornar la descarga del archivo PDF
        return $pdf->download("factura-pedido-{$pedido->id}.pdf");
    }
}