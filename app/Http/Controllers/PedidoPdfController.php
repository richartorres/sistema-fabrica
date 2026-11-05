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

   public function tirilla(Pedido $pedido)
    {
        $pedido->load('cliente');

        // Ajustar el tamaño del papel térmico (ancho 80mm = ~226 puntos, altura dinámica o ajustada)
        $pdf = Pdf::loadView('pdf.tirilla', compact('pedido'))
                  ->setPaper([0, 0, 226.77, 400], 'portrait');

        return $pdf->stream("tirilla-pedido-{$pedido->id}.pdf");
    }
    
}
