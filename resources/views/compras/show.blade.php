@extends('adminlte::page')

@section('title', 'Detalle de Compra')

@section('content_header')
    <h1>{{ $compra->numero_compra }} - Detalle de Compra de Ingredientes</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Información de la compra -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-basket"></i> Información de la Compra
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            @if($compra->estado == 'pendiente')
                                <a href="{{ route('compras.edit', $compra->id_compra) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <button class="btn btn-success" onclick="completarCompra()">
                                    <i class="fas fa-check"></i> Completar
                                </button>
                                <button class="btn btn-danger" onclick="cancelarCompra()">
                                    <i class="fas fa-times"></i> Cancelar
                                </button>
                            @endif
                            <a href="{{ route('compras.index') }}" class="btn btn-info">
                                <i class="fas fa-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Número de Compra:</strong></td>
                                    <td>{{ $compra->numero_compra }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Proveedor:</strong></td>
                                    <td>{{ $compra->proveedor->nombre }}</td>
                                </tr>
                                <tr>
                                    <td><strong>RUC:</strong></td>
                                    <td>{{ $compra->proveedor->ruc }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Contacto:</strong></td>
                                    <td>{{ $compra->proveedor->contacto ?: 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Teléfono:</strong></td>
                                    <td>{{ $compra->proveedor->telefono ?: 'No especificado' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Fecha de Compra:</strong></td>
                                    <td>{{ $compra->fecha_compra->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Entrega:</strong></td>
                                    <td>{{ $compra->fecha_entrega ? $compra->fecha_entrega->format('d/m/Y') : 'No especificada' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo de Compra:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $compra->tipo_compra == 'contado' ? 'success' : 'warning' }}">
                                            {{ ucfirst($compra->tipo_compra) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Número de Factura:</strong></td>
                                    <td>{{ $compra->numero_factura ?: 'No especificada' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>{!! $compra->estado_badge !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($compra->observaciones)
                        <div class="alert alert-info">
                            <strong>Observaciones:</strong> {{ $compra->observaciones }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ingredientes de la compra -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-seedling"></i> Ingredientes Comprados
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Ingrediente</th>
                                    <th>Categoría</th>
                                    <th>Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                    <th>Stock Actual</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($compra->detalles as $detalle)
                                <tr>
                                    <td>
                                        <strong>{{ $detalle->getNombreIngrediente() }}</strong>
                                        @if($detalle->isStockBajo())
                                            <br><span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Stock Bajo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($detalle->getCategoriaIngrediente()) }}</span>
                                    </td>
                                    <td>{{ number_format($detalle->cantidad, 2) }}</td>
                                    <td>
                                        <small class="badge badge-secondary">{{ $detalle->getUnidadMedida() }}</small>
                                    </td>
                                    <td>S/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td><strong>S/. {{ number_format($detalle->subtotal, 2) }}</strong></td>
                                    <td>
                                        @if($detalle->tieneAlmacen())
                                            <span class="badge badge-success">{{ number_format($detalle->getStockActual(), 2) }}</span>
                                            <br><small class="text-muted">{{ $detalle->getUnidadMedida() }}</small>
                                        @else
                                            <span class="badge badge-warning">Sin stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($detalle->observaciones)
                                            <small>{{ $detalle->observaciones }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="5"><strong>Subtotal:</strong></td>
                                    <td><strong>S/. {{ number_format($compra->subtotal, 2) }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr class="bg-light">
                                    <td colspan="5"><strong>IGV (18%):</strong></td>
                                    <td><strong>S/. {{ number_format($compra->impuesto, 2) }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                                <tr class="bg-primary text-white">
                                    <td colspan="5"><strong>TOTAL:</strong></td>
                                    <td><strong>S/. {{ number_format($compra->total, 2) }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel lateral con información adicional -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Información Adicional
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-blue">
                            <i class="fas fa-user"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Registrado por</span>
                            <span class="info-box-number">{{ $compra->usuario->name }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-green">
                            <i class="fas fa-calendar"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Fecha de Registro</span>
                            <span class="info-box-number">{{ $compra->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-{{ $compra->aplicar_almacen ? 'success' : 'warning' }}">
                            <i class="fas fa-warehouse"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Aplicación Almacén</span>
                            <span class="info-box-number">{{ $compra->aplicar_almacen ? 'Automático' : 'Manual' }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-orange">
                            <i class="fas fa-seedling"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Ingredientes</span>
                            <span class="info-box-number">{{ $compra->detalles->count() }}</span>
                        </div>
                    </div>

                    @if($compra->estado == 'completada' && $compra->aplicar_almacen)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>¡Compra aplicada al almacén!</strong>
                            <br>Los ingredientes ya fueron agregados al inventario.
                        </div>
                    @elseif($compra->estado == 'pendiente' && $compra->aplicar_almacen)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Compra pendiente</strong>
                            <br>Al completar se aplicará automáticamente al almacén.
                        </div>
                    @elseif($compra->estado == 'completada' && !$compra->aplicar_almacen)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Aplicación manual requerida</strong>
                            <br>Deberás agregar manualmente los ingredientes al almacén.
                        </div>
                    @endif

                    <!-- Resumen de ingredientes -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Resumen de Ingredientes</h6>
                        </div>
                        <div class="card-body p-2">
                            <ul class="list-unstyled mb-0">
                                @foreach($compra->detalles as $detalle)
                                    <li class="mb-2">
                                        <small>
                                            <strong>{{ number_format($detalle->cantidad, 2) }} {{ $detalle->getUnidadMedida() }}</strong> 
                                            {{ $detalle->getNombreIngrediente() }}
                                            <span class="badge badge-info badge-sm ml-1">{{ ucfirst($detalle->getCategoriaIngrediente()) }}</span>
                                            @if($detalle->isStockBajo())
                                                <span class="badge badge-warning badge-sm ml-1"><i class="fas fa-exclamation-triangle"></i></span>
                                            @endif
                                            <br><span class="text-muted">S/. {{ number_format($detalle->subtotal, 2) }}</span>
                                        </small>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Estadísticas por categoría -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title">Por Categoría</h6>
                        </div>
                        <div class="card-body p-2">
                            @php
                                $categorias = $compra->detalles->groupBy(function($item) {
                                    return $item->getCategoriaIngrediente();
                                });
                            @endphp
                            @foreach($categorias as $categoria => $items)
                                <div class="mb-1">
                                    <small>
                                        <strong>{{ ucfirst($categoria) }}</strong>: {{ $items->count() }} ingrediente(s)
                                        <br><span class="text-muted">S/. {{ number_format($items->sum('subtotal'), 2) }}</span>
                                    </small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function completarCompra() {
        if (confirm('¿Estás seguro de completar esta compra? {{ $compra->aplicar_almacen ? "Los ingredientes se agregarán automáticamente al almacén." : "Deberás agregar manualmente los ingredientes al almacén." }}')) {
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            $.ajax({
                url: `/admin/compras/{{ $compra->id_compra }}/completar`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    let message = 'Error al completar compra';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }
    }

    function cancelarCompra() {
        if (confirm('¿Estás seguro de cancelar esta compra? Esta acción no se puede deshacer.')) {
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            $.ajax({
                url: `/admin/compras/{{ $compra->id_compra }}/cancelar`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    let message = 'Error al cancelar compra';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }
    }
</script>
@endsection