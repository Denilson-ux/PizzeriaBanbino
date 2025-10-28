@extends('adminlte::page')

@section('title', 'Ingredientes')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Gestión de Ingredientes</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Ingredientes</li>
            </ol>
        </div>
    </div>
@stop

@section('content')

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <i class="icon fas fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i> Lista de Ingredientes
            </h3>
            <div class="card-tools">
                <a href="{{ route('ingredientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Ingrediente
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Unidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingredientes as $ingrediente)
                        <tr>
                            <td>
                                <strong>{{ $ingrediente->nombre }}</strong>
                                @if($ingrediente->descripcion)
                                    <br><small class="text-muted">{{ Str::limit($ingrediente->descripcion, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($ingrediente->categoria ?? 'Otros') }}</span>
                            </td>
                            <td>
                                <small class="badge badge-secondary">{{ $ingrediente->unidad_medida }}</small>
                            </td>
                            <td>
                                @if($ingrediente->estado == 'activo')
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('ingredientes.show', $ingrediente->id_ingrediente) }}" 
                                       class="btn btn-info btn-sm" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('ingredientes.edit', $ingrediente->id_ingrediente) }}" 
                                       class="btn btn-warning btn-sm" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('ingredientes.destroy', $ingrediente->id_ingrediente) }}" style="display:inline" onsubmit="return confirm('¿Eliminar este ingrediente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                <div class="py-4">
                                    <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No hay ingredientes registrados</h5>
                                    <p class="text-muted">Comienza agregando tu primer ingrediente</p>
                                    <a href="{{ route('ingredientes.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Crear Ingrediente
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($ingredientes->hasPages())
        <div class="card-footer">
            {{ $ingredientes->links() }}
        </div>
        @endif
    </div>
</div>

@endsection