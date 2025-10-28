@extends('adminlte::page')

@section('title', 'Detalle de Proveedor')

@section('content_header')
    <h1>{{ $proveedor->nombre }}</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <!-- Información del proveedor -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-truck"></i> Información del Proveedor
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <a href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="{{ route('compras.create') }}?proveedor={{ $proveedor->id_proveedor }}" class="btn btn-success">
                                <i class="fas fa-shopping-basket"></i> Nueva Compra
                            </a>
                            <a href="{{ route('proveedores.index') }}" class="btn btn-info">
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
                                    <td><strong>Nombre/Razón Social:</strong></td>
                                    <td>{{ $proveedor->nombre }}</td>
                                </tr>
                                <tr>
                                    <td><strong>RUC:</strong></td>
                                    <td>{{ $proveedor->ruc }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Teléfono:</strong></td>
                                    <td>{{ $proveedor->telefono ?: 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $proveedor->email ?: 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Persona de Contacto:</strong></td>
                                    <td>{{ $proveedor->contacto ?: 'No especificado' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $proveedor->estado == 'activo' ? 'success' : 'danger' }} badge-lg">
                                            {{ ucfirst($proveedor->estado) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Registro:</strong></td>
                                    <td>{{ $proveedor->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dirección:</strong></td>
                                    <td>{{ $proveedor->direccion ?: 'No especificada' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de compras -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Historial de Compras
                    </h3>
                </div>
                <div class="card-body">
                    @if($proveedor->compras->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Número</th>
                                        <th>Fecha</th>
                                        <th>Productos</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proveedor->compras->sortByDesc('created_at') as $compra)
                                    <tr>
                                        <td>
                                            <strong>{{ $compra->numero_compra }}</strong>
                                            @if($compra->numero_factura)
                                                <br><small class="text-muted">F: {{ $compra->numero_factura }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $compra->fecha_compra->format('d/m/Y') }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $compra->detalles->count() }} items</span>
                                            <br><small class="text-muted">{{ $compra->detalles->sum('cantidad') }} unidades</small>
                                        </td>
                                        <td>
                                            <strong>S/. {{ number_format($compra->total, 2) }}</strong>
                                            @if($compra->tipo_compra == 'credito')
                                                <br><small class="badge badge-warning">Crédito</small>
                                            @endif
                                        </td>
                                        <td>{!! $compra->estado_badge !!}</td>
                                        <td>
                                            <a href="{{ route('compras.show', $compra->id_compra) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-light">
                                        <td colspan="3"><strong>Total compras completadas:</strong></td>
                                        <td><strong>S/. {{ number_format($estadisticas['monto_total'], 2) }}</strong></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>No hay compras registradas</h5>
                            <p>Este proveedor aún no tiene compras en el sistema.</p>
                            <a href="{{ route('compras.create') }}?proveedor={{ $proveedor->id_proveedor }}" class="btn btn-success">
                                <i class="fas fa-plus"></i> Crear Primera Compra
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estadísticas del proveedor -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Estadísticas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info">
                            <i class="fas fa-shopping-basket"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Compras</span>
                            <span class="info-box-number">{{ $estadisticas['total_compras'] }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Completadas</span>
                            <span class="info-box-number">{{ $estadisticas['compras_completadas'] }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-warning">
                            <i class="fas fa-clock"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pendientes</span>
                            <span class="info-box-number">{{ $estadisticas['compras_pendientes'] }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-primary">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Monto Total</span>
                            <span class="info-box-number">S/. {{ number_format($estadisticas['monto_total'], 0) }}</span>
                        </div>
                    </div>

                    @if($estadisticas['promedio_compra'] > 0)
                    <div class="info-box">
                        <span class="info-box-icon bg-secondary">
                            <i class="fas fa-calculator"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Promedio por Compra</span>
                            <span class="info-box-number">S/. {{ number_format($estadisticas['promedio_compra'], 0) }}</span>
                        </div>
                    </div>
                    @endif

                    @if($estadisticas['ultima_compra'])
                        <div class="alert alert-info">
                            <i class="fas fa-calendar-alt"></i>
                            <strong>Última compra:</strong>
                            <br>{{ $estadisticas['ultima_compra']->format('d/m/Y') }}
                            <br><small class="text-muted">Hace {{ $estadisticas['ultima_compra']->diffForHumans() }}</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones rápidas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="{{ route('compras.create') }}?proveedor={{ $proveedor->id_proveedor }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-plus text-success"></i> Nueva Compra a este Proveedor
                        </a>
                        <a href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit text-warning"></i> Editar Información
                        </a>
                        <button class="list-group-item list-group-item-action" onclick="cambiarEstado()">
                            <i class="fas fa-{{ $proveedor->estado == 'activo' ? 'ban text-danger' : 'check text-success' }}"></i> 
                            {{ $proveedor->estado == 'activo' ? 'Desactivar' : 'Activar' }} Proveedor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function cambiarEstado() {
        const accion = '{{ $proveedor->estado }}' === 'activo' ? 'desactivar' : 'activar';
        if (confirm(`¿Estás seguro de ${accion} el proveedor "{{ $proveedor->nombre }}"?`)) {
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            $.ajax({
                url: `/admin/proveedores/{{ $proveedor->id_proveedor }}/cambiar-estado`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    let message = 'Error al cambiar estado';
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