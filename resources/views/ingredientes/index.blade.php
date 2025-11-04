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
        
        <div class="card-body table-responsive p-0">
            @if(isset($ingredientes) && $ingredientes->count() > 0)
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Ingrediente</th>
                            <th style="width: 20%;">Categoría</th>
                            <th style="width: 15%;">Unidad</th>
                            <th style="width: 15%;">Estado</th>
                            <th style="width: 20%;">Acciones</th>
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
                                @php
                                    $categoriaColors = [
                                        'lacteos' => 'primary',
                                        'carnes' => 'danger', 
                                        'vegetales' => 'success',
                                        'harinas' => 'warning',
                                        'condimentos' => 'info',
                                        'bebidas' => 'secondary',
                                        'otros' => 'dark'
                                    ];
                                    $color = $categoriaColors[$ingrediente->categoria] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ ucfirst($ingrediente->categoria ?? 'Otros') }}</span>
                            </td>
                            <td>
                                <span class="badge badge-light">{{ $ingrediente->unidad_medida }}</span>
                            </td>
                            <td>
                                @if($ingrediente->estado == 'activo')
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-danger">Inactivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('ingredientes.show', $ingrediente->id_ingrediente) }}" 
                                       class="btn btn-info" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('ingredientes.edit', $ingrediente->id_ingrediente) }}" 
                                       class="btn btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-danger btn-delete" 
                                            title="Eliminar"
                                            data-id="{{ $ingrediente->id_ingrediente }}"
                                            data-nombre="{{ $ingrediente->nombre }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <form id="delete-form-{{ $ingrediente->id_ingrediente }}" 
                                      action="{{ route('ingredientes.destroy', $ingrediente->id_ingrediente) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-5">
                    <div class="py-4">
                        <i class="fas fa-seedling fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay ingredientes registrados</h5>
                        <p class="text-muted">Comienza agregando tu primer ingrediente</p>
                        <a href="{{ route('ingredientes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Crear Ingrediente
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        @if(isset($ingredientes) && $ingredientes->hasPages())
        <div class="card-footer clearfix">
            <div class="float-left">
                <small class="text-muted">
                    Mostrando {{ $ingredientes->firstItem() }} a {{ $ingredientes->lastItem() }} 
                    de {{ $ingredientes->total() }} ingredientes
                </small>
            </div>
            <div class="float-right">
                {{ $ingredientes->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el ingrediente <strong id="ingrediente-nombre"></strong>?</p>
                <p class="text-muted">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirm-delete">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<style>
    .table-head-fixed thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    .btn-group-sm > .btn, .btn-sm {
        padding: .25rem .5rem;
        font-size: .875rem;
        border-radius: .2rem;
    }
    
    .badge {
        font-size: 0.75em;
        font-weight: 500;
    }
    
    .card-tools {
        margin-left: auto;
    }
    
    .pagination {
        margin: 0;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #007bff;
        background-color: #fff;
        border: 1px solid #dee2e6;
        font-size: 0.875rem;
    }
    
    .pagination .page-item.active .page-link {
        z-index: 3;
        color: #fff;
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .pagination .page-link:hover {
        z-index: 2;
        color: #0056b3;
        text-decoration: none;
        background-color: #e9ecef;
        border-color: #dee2e6;
    }
    
    .table td, .table th {
        vertical-align: middle;
    }
    
    @media (max-width: 768px) {
        .table-responsive .table {
            font-size: 0.875rem;
        }
        
        .btn-group-sm > .btn {
            padding: .125rem .25rem;
            font-size: 0.75rem;
        }
        
        .card-tools {
            margin-top: 0.5rem;
            margin-left: 0;
        }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[title]').tooltip();
    
    // Manejar clic en botón eliminar
    $('.btn-delete').on('click', function() {
        const ingredienteId = $(this).data('id');
        const ingredienteNombre = $(this).data('nombre');
        
        $('#ingrediente-nombre').text(ingredienteNombre);
        $('#confirm-delete').data('id', ingredienteId);
        $('#deleteModal').modal('show');
    });
    
    // Confirmar eliminación
    $('#confirm-delete').on('click', function() {
        const ingredienteId = $(this).data('id');
        $('#delete-form-' + ingredienteId).submit();
    });
    
    // Auto-ocultar alertas después de 5 segundos
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});
</script>
@endsection