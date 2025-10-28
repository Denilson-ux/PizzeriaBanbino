@extends('adminlte::page')

@section('title', 'Gestión de Proveedores')

@section('content_header')
    <h1>Gestión de Proveedores</h1>
@stop

@section('content')

<div class="container-fluid">
    <!-- Tarjetas de estadísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $estadisticas['total_proveedores'] }}</h3>
                    <p>Total Proveedores</p>
                </div>
                <div class="icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $estadisticas['activos'] }}</h3>
                    <p>Proveedores Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $estadisticas['inactivos'] }}</h3>
                    <p>Proveedores Inactivos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $estadisticas['con_compras'] }}</h3>
                    <p>Con Compras</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-truck"></i> Lista de Proveedores
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Proveedor
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="row mb-3">
                        <div class="col-md-4">
                            <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, RUC, email..." value="{{ request('buscar') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="estado" class="form-control">
                                <option value="">Todos los estados</option>
                                <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                                <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="proveedores-table" class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Información</th>
                                    <th>Contacto</th>
                                    <th>Compras</th>
                                    <th>Estado</th>
                                    <th>Fecha Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proveedores as $proveedor)
                                <tr>
                                    <td>
                                        <strong>{{ $proveedor->nombre }}</strong>
                                        <br><small class="text-muted">RUC: {{ $proveedor->ruc }}</small>
                                        @if($proveedor->contacto)
                                            <br><small class="text-info">Contacto: {{ $proveedor->contacto }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($proveedor->telefono)
                                            <i class="fas fa-phone"></i> {{ $proveedor->telefono }}<br>
                                        @endif
                                        @if($proveedor->email)
                                            <i class="fas fa-envelope"></i> {{ $proveedor->email }}<br>
                                        @endif
                                        @if($proveedor->direccion)
                                            <i class="fas fa-map-marker-alt"></i> {{ Str::limit($proveedor->direccion, 30) }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $proveedor->compras_count }} compras</span>
                                        @if($proveedor->total_compras > 0)
                                            <br><small>Total: S/. {{ number_format($proveedor->total_compras, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $proveedor->estado == 'activo' ? 'success' : 'danger' }}">
                                            {{ ucfirst($proveedor->estado) }}
                                        </span>
                                    </td>
                                    <td>{{ $proveedor->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('proveedores.show', $proveedor->id_proveedor) }}" class="btn btn-info" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('proveedores.edit', $proveedor->id_proveedor) }}" class="btn btn-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-{{ $proveedor->estado == 'activo' ? 'secondary' : 'success' }}" 
                                                    onclick="cambiarEstado({{ $proveedor->id_proveedor }}, '{{ $proveedor->nombre }}')" 
                                                    title="{{ $proveedor->estado == 'activo' ? 'Desactivar' : 'Activar' }}">
                                                <i class="fas fa-{{ $proveedor->estado == 'activo' ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center">
                        {{ $proveedores->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
    function cambiarEstado(id, nombre) {
        if (confirm(`¿Estás seguro de cambiar el estado del proveedor "${nombre}"?`)) {
            const formData = new FormData();
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            $.ajax({
                url: `/admin/proveedores/${id}/cambiar-estado`,
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