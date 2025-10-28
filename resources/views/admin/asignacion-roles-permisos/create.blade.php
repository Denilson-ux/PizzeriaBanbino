@extends('adminlte::page')

@section('title', 'Asignar Roles y Permisos')

@section('content_header')
    <h1>Asignar Roles y Permisos a Usuario</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Formulario de Asignación</h3>
                    <div class="card-tools">
                        <a href="{{ route('asignacion-roles-permisos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <form action="{{ route('asignacion-roles-permisos.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <!-- Selección de Usuario -->
                        <div class="form-group">
                            <label for="user_id">Usuario *</label>
                            <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">Seleccione un usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                                       {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">
                                                    <strong>{{ $role->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $role->permissions->count() }} permisos</small>
                                                </label>
                                            </div>
                                            <!-- Mostrar permisos del rol -->
                                            <div class="role-permissions ml-3 mb-3" id="permissions_role_{{ $role->id }}" style="display: none;">
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
                                                       {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vista previa de asignaciones actuales del usuario -->
                        <div class="form-group" id="current-assignments" style="display: none;">
                            <label>Asignaciones Actuales del Usuario</label>
                            <div class="card border-warning">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Roles Actuales:</strong>
                                            <div id="current-roles"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Permisos Directos Actuales:</strong>
                                            <div id="current-permissions"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Asignar Roles y Permisos
                        </button>
                        <a href="{{ route('asignacion-roles-permisos.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
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
        .current-assignment-item {
            display: inline-block;
            margin: 2px;
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

            // Cargar asignaciones actuales cuando se selecciona un usuario
            $('#user_id').change(function() {
                const userId = $(this).val();
                if (userId) {
                    loadCurrentAssignments(userId);
                } else {
                    $('#current-assignments').hide();
                }
            });

            function loadCurrentAssignments(userId) {
                // Esta función debería hacer una llamada AJAX para obtener las asignaciones actuales
                // Por ahora, simplemente mostramos la sección
                $.ajax({
                    url: `/admin/users/${userId}/assignments`,
                    type: 'GET',
                    success: function(data) {
                        displayCurrentAssignments(data);
                    },
                    error: function() {
                        console.log('Error al cargar asignaciones actuales');
                    }
                });
            }

            function displayCurrentAssignments(data) {
                let rolesHtml = '';
                let permissionsHtml = '';

                if (data.roles && data.roles.length > 0) {
                    data.roles.forEach(function(role) {
                        rolesHtml += `<span class="badge badge-primary current-assignment-item">${role.name}</span>`;
                    });
                } else {
                    rolesHtml = '<span class="text-muted">Sin roles asignados</span>';
                }

                if (data.permissions && data.permissions.length > 0) {
                    data.permissions.forEach(function(permission) {
                        permissionsHtml += `<span class="badge badge-info current-assignment-item">${permission.name}</span>`;
                    });
                } else {
                    permissionsHtml = '<span class="text-muted">Sin permisos directos</span>';
                }

                $('#current-roles').html(rolesHtml);
                $('#current-permissions').html(permissionsHtml);
                $('#current-assignments').show();
            }
        });
    </script>
@stop