<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Compras</title>
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
            border-bottom: 3px solid #007bff;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
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
            background-color: #007bff;
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
        .status-completada {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelada {
            background-color: #f8d7da;
            color: #721c24;
        }
        .details-section {
            margin-top: 15px;
            padding: 10px;
            background-color: #fafafa;
            border-left: 3px solid #007bff;
            font-size: 13px;
        }
        .details-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🛍️ Pizzería Bambino</div>
            <h2>Reporte de Compras</h2>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Período:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</span>
            </div>
            @if($proveedorNombre)
            <div class="info-row">
                <span class="info-label">Proveedor:</span>
                <span class="info-value">{{ $proveedorNombre }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Total de Compras:</span>
                <span class="info-value">{{ $totalCompras }}</span>
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

        @if($compras->count() > 0)
        <div class="table-container">
            @foreach($compras as $compra)
            <table style="margin-bottom: 30px;">
                <thead>
                    <tr>
                        <th colspan="5" style="background-color: #0056b3; text-align: left; font-size: 14px;">
                            Compra #{{ $compra->numero_compra }} - {{ \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y') }}
                        </th>
                    </tr>
                    <tr>
                        <th>Ingrediente</th>
                        <th>Cantidad</th>
                        <th>Precio Unit.</th>
                        <th>Subtotal</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subtotalCompra = 0;
                    @endphp
                    @foreach($compra->detalles as $detalle)
                    @php
                        $subtotal = $detalle->cantidad * $detalle->precio_unitario;
                        $subtotalCompra += $subtotal;
                    @endphp
                    <tr>
                        <td>
                            @if($detalle->ingrediente)
                                <strong>{{ $detalle->ingrediente->nombre }}</strong>
                                @if($detalle->ingrediente->categoria)
                                    <br><small style="color: #666;">({{ $detalle->ingrediente->categoria }})</small>
                                @endif
                            @else
                                Ingrediente no disponible
                            @endif
                        </td>
                        <td>{{ number_format($detalle->cantidad, 2) }} {{ $detalle->ingrediente->unidad_medida ?? '' }}</td>
                        <td>Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>Bs. {{ number_format($subtotal, 2) }}</td>
                        <td>
                            @if($detalle->observaciones)
                                <small>{{ $detalle->observaciones }}</small>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3"><strong>SUBTOTAL COMPRA</strong></td>
                        <td colspan="2"><strong>Bs. {{ number_format($subtotalCompra, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>

            <div class="details-section">
                <div class="details-title">Información de la Compra</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div>
                        <strong>Proveedor:</strong> {{ $compra->proveedor->nombre ?? 'No especificado' }}
                    </div>
                    <div>
                        <strong>Estado:</strong> 
                        @php
                            $statusClass = 'status-pendiente';
                            if(strtolower($compra->estado) == 'completada') $statusClass = 'status-completada';
                            if(strtolower($compra->estado) == 'cancelada') $statusClass = 'status-cancelada';
                        @endphp
                        <span class="status-badge {{ $statusClass }}">{{ ucfirst($compra->estado) }}</span>
                    </div>
                    <div>
                        <strong>Tipo:</strong> {{ ucfirst($compra->tipo_compra) }}
                    </div>
                    <div>
                        <strong>Factura:</strong> {{ $compra->numero_factura ?? 'Sin factura' }}
                    </div>
                    @if($compra->almacenDestino)
                    <div>
                        <strong>Almacén:</strong> {{ $compra->almacenDestino->nombre }}
                    </div>
                    @endif
                    @if($compra->fecha_entrega)
                    <div>
                        <strong>Fecha Entrega:</strong> {{ \Carbon\Carbon::parse($compra->fecha_entrega)->format('d/m/Y') }}
                    </div>
                    @endif
                    @if($compra->usuario)
                    <div style="grid-column: 1 / -1;">
                        <strong>Registrado por:</strong> {{ $compra->usuario->name }}
                    </div>
                    @endif
                    @if($compra->observaciones)
                    <div style="grid-column: 1 / -1;">
                        <strong>Observaciones:</strong> {{ $compra->observaciones }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div style="background-color: #e8f4f8; padding: 20px; border-radius: 8px; margin-top: 30px;">
            <div style="text-align: center;">
                <h3 style="color: #007bff; margin: 0 0 10px 0;">RESUMEN TOTAL</h3>
                <p style="font-size: 24px; font-weight: bold; margin: 0; color: #333;">
                    Bs. {{ number_format($montoTotal, 2) }}
                </p>
                <p style="margin: 5px 0 0 0; color: #666;">
                    {{ $totalCompras }} compra{{ $totalCompras != 1 ? 's' : '' }} en el período
                </p>
            </div>
        </div>
        @else
        <div class="empty-state">
            <h3>No se encontraron compras</h3>
            <p>No hay compras que coincidan con los criterios de búsqueda especificados.</p>
        </div>
        @endif

        <div class="footer">
            <p>Este reporte fue generado automáticamente por el sistema de Pizzería Bambino</p>
            <p>Fecha: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>