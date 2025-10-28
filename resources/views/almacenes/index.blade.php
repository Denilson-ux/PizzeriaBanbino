@extends('adminlte::page')

@section('title', 'Gestión de Almacenes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Gestión de Almacenes</h1>
        <a href="{{ route('almacenes.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Nuevo Almacén
        </a>
    </div>
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle"></i> Se encontraron errores
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif

<div class="row">
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $estadisticas['total_almacenes'] }}" text="Total Almacenes" icon="fas fa-warehouse" theme="info"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $estadisticas['almacenes_activos'] }}" text="Almacenes Activos" icon="fas fa-check" theme="success"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $estadisticas['almacenes_inactivos'] }}" text="Almacenes Inactivos" icon="fas fa-times" theme="danger"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="{{ $estadisticas['total_inventario'] }}" text="Ítems en Inventario" icon="fas fa-boxes" theme="purple"/>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <i class="fas fa-filter"></i> Filtros
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('almacenes.index') }}" id="formFiltros">
            <div class="row">
                <div class="col-md-5 mb-2">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar por nombre, ubicación o responsable" value="{{ request('buscar') }}">
                </div>
                <div class="col-md-3 mb-2">
                    <select name="estado" class="form-control" onchange="document.getElementById('formFiltros').submit()">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('estado')=='activo'?'selected':'' }}>Activo</option>
                        <option value="inactivo" {{ request('estado')=='inactivo'?'selected':'' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Buscar</button>
                    <a href="{{ route('almacenes.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-list"></i> Lista de Almacenes</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th>Responsable</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Inventario</th>
                        <th width="140">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($almacenes as $almacen)
                    <tr>
                        <td>{{ $almacen->nombre }}</td>
                        <td>{{ $almacen->ubicacion ?? '—' }}</td>
                        <td>{{ $almacen->responsable ?? '—' }}</td>
                        <td>{{ $almacen->telefono ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $almacen->estado=='activo'?'success':'danger' }}">{{ ucfirst($almacen->estado) }}</span>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $almacen->total_ingredientes }} items</span>
                            @if($almacen->productos_stock_bajo>0)
                                <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> {{ $almacen->productos_stock_bajo }} bajo</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a class="btn btn-info" href="{{ route('almacenes.show', $almacen->id_almacen) }}"><i class="fas fa-eye"></i></a>
                                <a class="btn btn-warning" href="{{ route('almacenes.edit', $almacen->id_almacen) }}"><i class="fas fa-edit"></i></a>
                                @if(!$almacen->tieneIngredientes() && $almacen->compras()->count()==0)
                                <form method="POST" action="{{ route('almacenes.destroy', $almacen->id_almacen) }}" onsubmit="return confirm('¿Eliminar almacén?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-warehouse fa-2x mb-2"></i>
                            <div>No hay almacenes</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($almacenes->hasPages())
    <div class="card-footer">
        {{ $almacenes->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
