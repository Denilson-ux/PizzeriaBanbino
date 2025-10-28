@extends('adminlte::page')

@section('title', 'Detalles de Asignación')

@section('content_header')
    <h1>Detalles de Asignación: {{ $user->name }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Información del Usuario y Permisos</h3>
                    <div class="card-tools">
                        <a href="{{ route('asignacion-roles-permisos.edit', $user->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('asignacion-roles-permisos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Información del Usuario -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user"></i> Datos del Usuario
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID:</strong></td>
                                            <td>{{ $user->id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Registrado:</strong></td>
                                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total de Roles:</strong></td>
                                            <td>
                                                <span class="badge badge-primary badge-lg">{{ $user->roles->count() }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Permisos Directos:</strong></td>
                                            <td>
                                                <span class="badge badge-warning badge-lg">{{ $user->getDirectPermissions()->count() }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total de Permisos:</strong></td>
                                            <td>
                                                <span class="badge badge-success badge-lg">{{ $user->getAllPermissions()->count() }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-chart-pie"></i> Resumen de Accesos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-primary">
                                                    <i class="fas fa-user-tag"></i>
                                                </span>
                                                <h5 class="description-header">{{ $user->roles->count() }}</h5>
                                                <span class="description-text">ROLES</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-warning">
                                                    <i class="fas fa-key"></i>
                                                </span>
                                                <h5 class="description-header">{{ $user->getDirectPermissions()->count() }}</h5>
                                                <span class="description-text">DIRECTOS</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="description-block">
                                                <span class="description-percentage text-success">
                                                    <i class="fas fa-shield-alt"></i>
                                                </span>
                                                <h5 class="description-header">{{ $user->getAllPermissions()->count() }}</h5>
                                                <span class="description-text">TOTAL</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles Asignados -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user-shield"></i> Roles Asignados
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @forelse($user->roles as $role)
                                        <div class="callout callout-primary">
                                            <h5><i class="fas fa-user-tag"></i> {{ $role->name }}</h5>
                                            <p><strong>Permisos incluidos en este rol:</strong></p>
                                            <div class="row">
                                                @forelse($role->permissions->chunk(3) as $permissionChunk)
                                                    <div class="col-md-4">
                                                        @foreach($permissionChunk as $permission)
                                                            <span class="badge badge-secondary mb-1">
                                                                <i class="fas fa-check"></i> {{ $permission->name }}
                                                            </span><br>
                                                        @endforeach
                                                    </div>
                                                @empty
                                                    <div class="col-12">
                                                        <span class="text-muted">Este rol no tiene permisos asignados</span>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @empty
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Este usuario no tiene roles asignados.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permisos Directos -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-key"></i> Permisos Asignados Directamente
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @forelse($user->getDirectPermissions()->chunk(4) as $permissionChunk)
                                        <div class="row mb-2">
                                            @foreach($permissionChunk as $permission)
                                                <div class="col-md-3">
                                                    <span class="badge badge-warning badge-lg">
                                                        <i class="fas fa-key"></i> {{ $permission->name }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @empty
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Este usuario no tiene permisos asignados directamente. Solo tiene los permisos que obtiene a través de sus roles.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Todos los Permisos Efectivos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-shield-alt"></i> Todos los Permisos Efectivos
                                        <small class="float-right">{{ $user->getAllPermissions()->count() }} permisos totales</small>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @forelse($user->getAllPermissions()->chunk(ceil($user->getAllPermissions()->count()/3)) as $permissionChunk)
                                            <div class="col-md-4">
                                                @foreach($permissionChunk as $permission)
                                                    <div class="mb-2">
                                                        @if($user->getDirectPermissions()->contains('name', $permission->name))
                                                            <span class="badge badge-warning">
                                                                <i class="fas fa-key"></i> {{ $permission->name }}
                                                                <small>(Directo)</small>
                                                            </span>
                                                        @else
                                                            <span class="badge badge-success">
                                                                <i class="fas fa-user-tag"></i> {{ $permission->name }}
                                                                <small>(Por Rol)</small>
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    ¡ATENCIÓN! Este usuario no tiene ningún permiso asignado.
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Los permisos marcados como "Por Rol" se obtienen automáticamente al asignar roles al usuario.
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                Última actualización: {{ $user->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .badge-lg {
            font-size: 1em;
            padding: 8px 12px;
        }
        .description-block .description-header {
            font-size: 2rem;
            font-weight: bold;
        }
        .callout {
            border-left: 4px solid #007bff;
            background-color: #f8f9fa;
        }
    </style>
@stop