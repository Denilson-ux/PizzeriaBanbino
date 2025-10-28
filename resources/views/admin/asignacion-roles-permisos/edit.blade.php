@extends('adminlte::page')

@section('title', 'Editar Asignación de Roles y Permisos')

@section('content_header')
    <h1>Editar Asignación: {{ $user->name }}</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Editar Asignación de Roles y Permisos</h3>
                    <div class="card-tools">
                        <a href="{{ route('asignacion-roles-permisos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <form action="{{ route('asignacion-roles-permisos.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <!-- Información del Usuario -->
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Usuario Seleccionado</h5>
                            <strong>Nombre:</strong> {{ $user->name }}<br>
                            <strong>Email:</strong> {{ $user->email }}<br>
                            <strong>ID:</strong> {{ $user->id }}
                        </div>

                        <!-- Asignación de Roles -->
                        <div class="form-group">
                            <label>Roles</label>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($roles as $role)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="form-check">
                                                <input class="form-check-input role-checkbox" 
                                                       type="checkbox" 
                                                       value="{{ $role->name }}" 
                                                       id="role_{{ $role->id }}" 
                                                       name="roles[]" 
                                                       data-role-id="{{ $role->id }}"
                                                       {{ $user->hasRole($role->name) || in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    <strong>{{ $role->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $role->permissions->count() }} permisos</small>
                                                    @if($user->hasRole($role->name))
                                                        <span class="badge badge-success">Actual</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <!-- Mostrar permisos del rol -->
                                            <div class="role-permissions ml-3 mb-3" 
                                                 id="permissions_role_{{ $role->id }}" 
                                                 style="{{ $user->hasRole($role->name) || in_array($role->name, old('roles', [])) ? '' : 'display: none;' }}">
                                                <small class="text-info">Permisos incluidos:</small>
                                                <ul class="list-unstyled ml-2">
                                                    @foreach($role->permissions as $permission)
                                                    <li><small>- {{ $permission->name }}</small></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permisos Adicionales Directos -->
                        <div class="form-group">
                            <label>Permisos Adicionales (Directos)</label>
                            <small class="form-text text-muted">Estos permisos se asignan directamente al usuario, además de los permisos que obtiene de sus roles.</small>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($permissions->chunk(ceil($permissions->count()/3)) as $permissionChunk)
                                        <div class="col-md-4">
                                            @foreach($permissionChunk as $permission)
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       value="{{ $permission->name }}" 
                                                       id="permission_{{ $permission->id }}" 
                                                       name="permissions[]"
                                                       {{ $user->getDirectPermissions()->contains('name', $permission->name) || in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                    @if($user->getDirectPermissions()->contains('name', $permission->name))
                                                        <span class="badge badge-warning">Directo</span>
                                                    @endif
                                                    @if($user->hasPermissionTo($permission->name) && !$user->getDirectPermissions()->contains('name', $permission->name))
                                                        <span class="badge badge-info">Por Rol</span>
                                                    @endif
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen de Cambios -->
                        <div class="form-group">
                            <label>Estado Actual vs. Nuevo</label>
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Estado Actual:</h6>
                                            <div class="mb-2">
                                                <strong>Roles:</strong><br>
                                                @forelse($user->roles as $role)
                                                    <span class="badge badge-primary">{{ $role->name }}</span>
                                                @empty
                                                    <span class="text-muted">Sin roles</span>
                                                @endforelse
                                            </div>
                                            <div>
                                                <strong>Permisos Directos:</strong><br>
                                                @forelse($user->getDirectPermissions() as $permission)
                                                    <span class="badge badge-warning">{{ $permission->name }}</span>
                                                @empty
                                                    <span class="text-muted">Sin permisos directos</span>
                                                @endforelse
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Todos los Permisos Efectivos:</h6>
                                            <div class="max-height-200 overflow-auto">
                                                @foreach($user->getAllPermissions() as $permission)
                                                    <span class="badge badge-secondary mr-1 mb-1">{{ $permission->name }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Asignaciones
                        </button>
                        <a href="{{ route('asignacion-roles-permisos.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="button" class="btn btn-warning" onclick="resetToDefault()">
                            <i class="fas fa-undo"></i> Restaurar Valores Actuales
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <style>
        .role-permissions {
            background-color: #f8f9fa;
            border-left: 3px solid #007bff;
            padding: 10px;
            border-radius: 5px;
        }
        .form-check-label {
            cursor: pointer;
        }
        .max-height-200 {
            max-height: 200px;
        }
        .overflow-auto {
            overflow: auto;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Mostrar/ocultar permisos de roles
            $('.role-checkbox').change(function() {
                const roleId = $(this).data('role-id');
                const permissionsDiv = $(`#permissions_role_${roleId}`);
                
                if ($(this).is(':checked')) {
                    permissionsDiv.slideDown();
                } else {
                    permissionsDiv.slideUp();
                }
            });
        });

        function resetToDefault() {
            // Resetear checkboxes de roles a su estado original
            @foreach($roles as $role)
                document.getElementById('role_{{ $role->id }}').checked = {{ $user->hasRole($role->name) ? 'true' : 'false' }};
                @if($user->hasRole($role->name))
                    $('#permissions_role_{{ $role->id }}').show();
                @else
                    $('#permissions_role_{{ $role->id }}').hide();
                @endif
            @endforeach

            // Resetear checkboxes de permisos a su estado original
            @foreach($permissions as $permission)
                document.getElementById('permission_{{ $permission->id }}').checked = {{ $user->getDirectPermissions()->contains('name', $permission->name) ? 'true' : 'false' }};
            @endforeach
        }
    </script>
@stop