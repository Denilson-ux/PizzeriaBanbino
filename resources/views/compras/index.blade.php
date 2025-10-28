@extends('adminlte::page')

@section('title', 'Gestión de Compras')

@section('content_header')
    <h1>Gestión de Compras</h1>
@stop

@section('content')

<div class="container-fluid">
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $estadisticas['total_compras'] }}</h3>
                    <p>Total Compras</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $estadisticas['pendientes'] }}</h3>
                    <p>Compras Pendientes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $estadisticas['completadas'] }}</h3>
                    <p>Completadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>S/. {{ number_format($estadisticas['total_monto'], 0) }}</h3>
                    <p>Monto Total</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-basket"></i> Lista de Compras
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('compras.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Compra
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="card mb-3">
                        <div class="card-header collapsed" data-toggle="collapse" data-target="#filtros">
                            <h6 class="mb-0">
                                <i class="fas fa-filter"></i> Filtros de Búsqueda
                                <i class="fas fa-chevron-down float-right"></i>
                            </h6>
                        </div>
                        <div class="collapse {{ request()->hasAny(['buscar', 'estado', 'proveedor_id', 'fecha_inicio', 'fecha_fin']) ? 'show' : '' }}" id="filtros">
                            <div class="card-body">
                                <form method="GET" class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="buscar">Buscar</label>
                                            <input type="text" name="buscar" id="buscar" class="form-control" placeholder="Número, factura, proveedor..." value="{{ request('buscar') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="estado">Estado</label>
                                            <select name="estado" id="estado" class="form-control">
                                                <option value="">Todos</option>
                                                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                                                <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="proveedor_id">Proveedor</label>
                                            <select name="proveedor_id" id="proveedor_id" class="form-control">
                                                <option value="">Todos los proveedores</option>
                                                @foreach($proveedores as $proveedor)
                                                    <option value="{{ $proveedor->id_proveedor }}" {{ request('proveedor_id') == $proveedor->id_proveedor ? 'selected' : '' }}>
                                                        {{ $proveedor->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fecha_inicio">Desde</label>
                                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="fecha_fin">Hasta</label>
                                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Filtrar
                                        </button>
                                        <a href="{{ route('compras.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Limpiar
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Número</th>
                                    <th>Proveedor</th>
                                    <th>Fecha</th>
                                    <th>Productos</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Almacén</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($compras as $compra)
                                <tr>
                                    <td>
                                        <strong>{{ $compra->numero_compra }}</strong>
                                        @if($compra->numero_factura)
                                            <br><small class="text-muted">F: {{ $compra->numero_factura }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $compra->proveedor->nombre }}</strong>
                                        <br><small class="text-muted">{{ $compra->proveedor->ruc }}</small>
                                    </td>
                                    <td>
                                        {{ $compra->fecha_compra->format('d/m/Y') }}
                                        @if($compra->fecha_entrega)
                                            <br><small class="text-info">Entrega: {{ $compra->fecha_entrega->format('d/m/Y') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $compra->detalles->count() }} productos</span>
                                        <br><small class="text-muted">{{ $compra->detalles->sum('cantidad') }} unidades total</small>
                                    </td>
                                    <td>
                                        <strong>S/. {{ number_format($compra->total, 2) }}</strong>
                                        @if($compra->tipo_compra == 'credito')
                                            <br><small class="badge badge-warning">Crédito</small>
                                        @endif
                                    </td>
                                    <td>
                                        {!! $compra->estado_badge !!}
                                    </td>
                                    <td>
                                        @if($compra->aplicar_almacen)
                                            <span class="badge badge-success">Automático</span>
                                        @else
                                            <span class="badge badge-warning">Manual</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('compras.show', $compra->id_compra) }}" class="btn btn-info" title="Ver detalle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($compra->estado == 'pendiente')
                                                <a href="{{ route('compras.edit', $compra->id_compra) }}" class="btn btn-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-success" onclick="completarCompra({{ $compra->id_compra }}, '{{ $compra->numero_compra }}')" title="Completar">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        No hay compras registradas
                                        <br><a href="{{ route('compras.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus"></i> Crear Primera Compra
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $compras->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function completarCompra(id, numero) {
        if (confirm(`¿Estás seguro de completar la compra ${numero}? Esta acción actualizará el almacén automáticamente.`)) {
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            $.ajax({
                url: `/admin/compras/${id}/completar`,
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
</script>
@endsection