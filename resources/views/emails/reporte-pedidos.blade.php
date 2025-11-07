<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Pedidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #dc3545;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 10px;
        }
        .info-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .table-container {
            overflow-x: auto;
            margin: 25px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .total-row {
            font-weight: bold;
            background-color: #e8f4f8 !important;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .empty-state h3 {
            color: #999;
            margin-bottom: 10px;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-entregado {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelado {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🍕 Pizzería Bambino</div>
            <h2>Reporte de Pedidos</h2>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Período:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</span>
            </div>
            @if($clienteNombre)
            <div class="info-row">
                <span class="info-label">Cliente:</span>
                <span class="info-value">{{ $clienteNombre }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Total de Pedidos:</span>
                <span class="info-value">{{ $totalPedidos }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Monto Total:</span>
                <span class="info-value">Bs. {{ number_format($montoTotal, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha de Generación:</span>
                <span class="info-value">{{ now()->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>

        @if($pedidos->count() > 0)
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th>Pago</th>
                        <th>Repartidor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $pedido)
                    <tr>
                        <td>#{{ $pedido->id_pedido }}</td>
                        <td>{{ \Carbon\Carbon::parse($pedido->fecha)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($pedido->cliente && $pedido->cliente->persona)
                                {{ $pedido->cliente->persona->nombre }} {{ $pedido->cliente->persona->paterno }}{{ $pedido->cliente->persona->materno ? ' '.$pedido->cliente->persona->materno : '' }}
                            @else
                                Cliente no disponible
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = 'status-pendiente';
                                if(strtolower($pedido->estado_pedido) == 'entregado') $statusClass = 'status-entregado';
                                if(strtolower($pedido->estado_pedido) == 'cancelado') $statusClass = 'status-cancelado';
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $pedido->estado_pedido }}</span>
                        </td>
                        <td>Bs. {{ number_format($pedido->monto, 2) }}</td>
                        <td>
                            @if($pedido->tipoPago)
                                {{ $pedido->tipoPago->nombre }}
                            @else
                                No especificado
                            @endif
                        </td>
                        <td>
                            @if($pedido->repartidor && $pedido->repartidor->persona)
                                {{ $pedido->repartidor->persona->nombre }} {{ $pedido->repartidor->persona->paterno }}{{ $pedido->repartidor->persona->materno ? ' '.$pedido->repartidor->persona->materno : '' }}
                            @else
                                No asignado
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4"><strong>TOTAL</strong></td>
                        <td><strong>Bs. {{ number_format($montoTotal, 2) }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="empty-state">
            <h3>No se encontraron pedidos</h3>
            <p>No hay pedidos que coincidan con los criterios de búsqueda especificados.</p>
        </div>
        @endif

        <div class="footer">
            <p>Este reporte fue generado automáticamente por el sistema de Pizzería Bambino</p>
            <p>Fecha: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>