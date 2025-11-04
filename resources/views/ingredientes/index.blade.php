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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="fas fa-seedling"></i> Lista de Ingredientes
                </h3>
                <a href="{{ route('ingredientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Nuevo Ingrediente
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if(isset($ingredientes) && $ingredientes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 25%">Ingrediente</th>
                                <th style="width: 20%" class="text-center">Categoría</th>
                                <th style="width: 15%" class="text-center">Unidad</th>
                                <th style="width: 15%" class="text-center">Estado</th>
                                <th style="width: 25%" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ingredientes as $ingrediente)
                            <tr>
                                <td class="align-middle">
                                    <div>
                                        <strong class="text-dark">{{ $ingrediente->nombre }}</strong>
                                        @if($ingrediente->descripcion)
                                            <br><small class="text-muted">{{ Str::limit($ingrediente->descripcion, 60) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    @php
                                        $categoriaClass = [
                                            'lacteos' => 'primary',
                                            'carnes' => 'danger', 
                                            'vegetales' => 'success',
                                            'harinas' => 'warning',
                                            'condimentos' => 'info',
                                            'bebidas' => 'secondary',
                                            'otros' => 'dark'
                                        ];
                                        $class = $categoriaClass[$ingrediente->categoria] ?? 'secondary';
                                    @endphp
                                    <span class="badge badge-{{ $class }} px-3 py-2">
                                        {{ ucfirst($ingrediente->categoria ?? 'Otros') }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-outline-secondary px-3 py-2">
                                        {{ $ingrediente->unidad_medida }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    @if($ingrediente->estado == 'activo')
                                        <span class="badge badge-success px-3 py-2">
                                            <i class="fas fa-check-circle"></i> Activo
                                        </span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">
                                            <i class="fas fa-times-circle"></i> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group" role="group" aria-label="Acciones">
                                        <a href="{{ route('ingredientes.show', $ingrediente->id_ingrediente) }}" 
                                           class="btn btn-info btn-sm" 
                                           title="Ver detalles"
                                           data-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('ingredientes.edit', $ingrediente->id_ingrediente) }}" 
                                           class="btn btn-warning btn-sm" 
                                           title="Editar ingrediente"
                                           data-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm btn-delete" 
                                                title="Eliminar ingrediente"
                                                data-toggle="tooltip"
                                                data-id="{{ $ingrediente->id_ingrediente }}"
                                                data-nombre="{{ $ingrediente->nombre }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Formulario oculto para eliminación -->
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
                </div>
            @else
                <div class="text-center py-5">
                    <div class="py-4">
                        <i class="fas fa-seedling fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">No hay ingredientes registrados</h4>
                        <p class="text-muted mb-4">Aún no has agregado ningún ingrediente al sistema.</p>
                        <a href="{{ route('ingredientes.create') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus-circle"></i> Crear Primer Ingrediente
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        @if(isset($ingredientes) && $ingredientes->hasPages())
        <div class="card-footer bg-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        Mostrando {{ $ingredientes->firstItem() }} a {{ $ingredientes->lastItem() }} 
                        de {{ $ingredientes->total() }} ingredientes
                    </small>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        {{ $ingredientes->links() }}
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
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
    .card-primary.card-outline {
        border-top: 3px solid #007bff;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 15px 10px;
    }
    
    .table td {
        padding: 15px 10px;
        vertical-align: middle;
        border-top: 1px solid #dee2e6;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,.075);
    }
    
    .badge {
        font-size: 11px;
        font-weight: 500;
    }
    
    .btn-group .btn {
        margin: 0 1px;
        border-radius: 3px;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 14px;
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
        }
        
        .btn-group .btn {
            margin: 1px 0;
            width: 100%;
        }
        
        .card-header h3 {
            font-size: 1.1rem;
        }
    }
    
    .alert {
        border: none;
        border-radius: 6px;
    }
    
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
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