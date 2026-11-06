<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tirilla Pedido #{{ $pedido->id }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 11px;
            color: #000;
            width: 200px;
            margin: 0 auto;
            padding: 0;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .line { border-bottom: 1px dashed #000; margin: 6px 0; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { font-size: 10px; text-align: left; padding: 2px 0; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="center bold">
        FÁBRICA DE COLCHONES Y MUEBLES
    </div>
    <div class="center">NIT: 900.000.000-1</div>
    <div class="center">Medellín, Antioquia</div>
    
    <div class="line"></div>

    <div><strong>Pedido #:</strong> {{ $pedido->id }}</div>
    <div><strong>Fecha:</strong> {{ $pedido->created_at->format('d/m/Y H:i') }}</div>
    <div><strong>Cliente:</strong> {{ $pedido->cliente->nombre ?? 'N/A' }}</div>
    <div><strong>Entrega:</strong> {{ $pedido->fecha_entrega_prometida ?? 'Por definir' }}</div>

    <div class="line"></div>

    <table class="table">
        <thead>
            <tr>
                <th>Cant / Desc</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2"><strong>{{ $pedido->notas ?? 'Colchón / Base estándar' }}</strong></td>
            </tr>
            <tr>
                <td>{{ $pedido->cantidad }} x $ {{ number_format($pedido->precio_unitario, 0, ',', '.') }}</td>
                <td class="right">$ {{ number_format($pedido->precio_total, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="line"></div>

    <div class="bold">Estado Prod: {{ ucfirst(str_replace('_', ' ', $pedido->estado)) }}</div>
    <div class="bold">Estado Pago: {{ ucfirst($pedido->estado_pago ?? 'Pendiente') }}</div>

    <div class="line"></div>

    <!-- Desglose Financiero Actualizado -->
    <table class="table">
        <tr>
            <td><strong>TOTAL:</strong></td>
            <td class="right">$ {{ number_format($pedido->precio_total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>ABONADO:</strong></td>
            <td class="right">$ {{ number_format($pedido->abonos->sum('monto'), 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><strong>SALDO:</strong></td>
            <td class="right"><strong>$ {{ number_format($pedido->saldo_pendiente, 0, ',', '.') }}</strong></td>
        </tr>
    </table>

    <div class="line"></div>
    <div class="center" style="font-size: 10px;">
        ¡Gracias por su compra!<br>
        Comprobante de Remisión Interna
    </div>
</body>
</html>