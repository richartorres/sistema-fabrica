<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de Venta #{{ $pedido->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1a202c; font-size: 24px; }
        .details, .table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #cbd5e0; padding: 8px; text-align: left; }
        .table th { background-color: #f7fafc; }
        .total { text-align: right; font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sistema Fábrica - Muebles y Colchones</h1>
        <p>Remisión y Comprobante de Pedido</p>
    </div>

    <table class="details">
            <tr>
                <td><strong>Pedido #:</strong> {{ $pedido->id }}</td>
                <td><strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td><strong>Cliente:</strong> {{ $pedido->cliente->nombre ?? 'N/A' }}</td>
                <td><strong>Entrega Prometida:</strong> {{ $pedido->fecha_entrega_prometida ?? 'Por definir' }}</td>
            </tr>
            <tr>
                <td><strong>Estado Producción:</strong> {{ ucfirst($pedido->estado) }}</td>
                <td><strong>Estado Pago:</strong> {{ ucfirst($pedido->estado_pago ?? 'Pendiente') }}</td>
            </tr>
        </table></table>

    <table class="table">
        <thead>
            <tr>
                <th>Descripción / Notas</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $pedido->notas ?? 'Colchón / Base estándar' }}</td>
                <td>{{ $pedido->cantidad }}</td>
                <td>$ {{ number_format($pedido->precio_unitario, 2) }}</td>
                <td>$ {{ number_format($pedido->precio_total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p class="total">Total a Pagar: $ {{ number_format($pedido->precio_total, 2) }}</p>
</body>
</html>