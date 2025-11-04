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
                @if($ingredientes->count() > 0)
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th style="width: 30%;">Nombre</th>
                                <th style="width: 15%;">Categoría</th>
                                <th style="width: 15%;">Unidad</th>
                                <th style="width: 15%;">Estado</th>
                                <th style="width: 25%; text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ingredientes as $ingrediente)
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
                                    <span class="badge badge-secondary">{{ $ingrediente->unidad_medida }}</span>
                                </td>
                                <td>
                                    @if($ingrediente->estado == 'activo')
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('ingredientes.show', $ingrediente->id_ingrediente) }}" 
                                           class="btn btn-info btn-sm" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('ingredientes.edit', $ingrediente->id_ingrediente) }}" 
                                           class="btn btn-warning btn-sm" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('ingredientes.destroy', $ingrediente->id_ingrediente) }}" 
                                              style="display:inline" 
                                              onsubmit="return confirm('¿Está seguro de eliminar este ingrediente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-seedling fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No hay ingredientes registrados</h4>
                        <p class="text-muted">Comienza agregando tu primer ingrediente al sistema</p>
                        <a href="{{ route('ingredientes.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus"></i> Crear Primer Ingrediente
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        @if(isset($ingredientes) && $ingredientes->hasPages())
        <div class="card-footer">
            <div class="row">
                <div class="col-sm-12 col-md-5">
                    <div class="dataTables_info">
                        Mostrando {{ $ingredientes->firstItem() }} a {{ $ingredientes->lastItem() }} 
                        de {{ $ingredientes->total() }} ingredientes
                    </div>
                </div>
                <div class="col-sm-12 col-md-7">
                    <div class="float-right">
                        {{ $ingredientes->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@section('css')
<style>
    .table th, .table td {
        vertical-align: middle;
        padding: 12px 8px;
    }
    
    .btn-group .btn {
        margin: 0 2px;
    }
    
    @media (max-width: 768px) {
        .table-responsive table {
            font-size: 14px;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .btn-group .btn {
            margin: 2px 0;
            width: 100%;
        }
    }
    
    .card-header .card-tools {
        margin-left: auto;
    }
    
    .badge {
        font-size: 12px;
        padding: 4px 8px;
    }
</style>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Confirmar eliminación
        $('.btn-danger').on('click', function(e) {
            if (!confirm('¿Está seguro de eliminar este ingrediente? Esta acción no se puede deshacer.')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Tooltips
        $('[title]').tooltip();
    });
</script>
@endsection