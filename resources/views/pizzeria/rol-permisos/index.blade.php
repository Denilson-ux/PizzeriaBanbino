@extends('adminlte::page')

@section('title', 'Gestión de Roles y Permisos')

@section('content_header')
    <h1>Gestión de Roles y Permisos</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Roles con Permisos</h3>
                    <div class="card-tools">
                        <button class="btn btn-success" onclick="showCreateModal()">
                            <i class="fas fa-plus"></i> Nuevo Rol
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="roles-table" class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Nombre del Rol</th>
                                    <th width="50%">Permisos Asignados</th>
                                    <th width="10%">N° Permisos</th>
                                    <th width="10%">Usuarios</th>
                                    <th width="10%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="roles-tbody">
                                <!-- Los datos se cargarán vía AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear/Editar Rol -->
<div class="modal fade" id="roleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="roleModalTitle">Crear Nuevo Rol</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="roleForm">
                <div class="modal-body">
                    <input type="hidden" id="role_id" name="role_id">
                    
                    <div class="row">
                        <!-- Nombre del rol -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="role_name">Nombre del Rol *</label>
                                <input type="text" class="form-control" id="role_name" name="name" 
                                       placeholder="Ej: Encargado de Almacén" required>
                                <small class="text-muted">El nombre debe ser único</small>
                            </div>
                        </div>
                        
                        <!-- Permisos disponibles -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Permisos para este Rol</label>
                                <small class="text-muted d-block mb-3">Selecciona los permisos que tendrá este rol:</small>
                                
                                <div class="card">
                                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                        <div class="row" id="permissions-container">
                                            <!-- Se llenarán dinámicamente -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Resumen de permisos seleccionados -->
                    <div class="row" id="selected-permissions-summary" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <strong>Permisos Seleccionados:</strong>
                                <div id="selected-permissions-list"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success" id="saveRoleBtn">
                        <i class="fas fa-save"></i> Crear Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('css')
    <style>
        .permission-checkbox {
            margin-bottom: 8px;
        }
        .permission-label {
            font-weight: normal;
            margin-left: 5px;
        }
        .badge-permission {
            font-size: 11px;
            margin: 2px;
            padding: 4px 8px;
        }
        .table th {
            vertical-align: middle;
        }
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('js')
<script>
    let rolesData = [];
    let permissionsData = [];

    $(document).ready(function() {
        // Cargar datos iniciales
        loadRoles();
        loadPermissions();
        
        // Manejar envío del formulario
        $('#roleForm').on('submit', function(e) {
            e.preventDefault();
            saveRole();
        });
        
        // Manejar cambios en checkboxes de permisos
        $(document).on('change', 'input[name="permissions[]"]', function() {
            updateSelectedPermissionsSummary();
        });
    });

    function loadRoles() {
        $.ajax({
            url: '/admin/api/roles-spatie',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Roles response:', response);
                if (response.success) {
                    rolesData = response.data;
                    populateRolesTable();
                } else {
                    showNotification('Error al cargar roles', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading roles:', xhr.responseText);
                showNotification('Error de conexión al cargar roles', 'error');
            }
        });
    }

    function loadPermissions() {
        $.ajax({
            url: '/admin/api/permisos-spatie',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Permissions response:', response);
                if (response.success) {
                    permissionsData = response.data;
                } else {
                    showNotification('Error al cargar permisos', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading permissions:', xhr.responseText);
                showNotification('Error de conexión al cargar permisos', 'error');
            }
        });
    }

    function populateRolesTable() {
        const tbody = $('#roles-tbody');
        tbody.empty();
        
        if (rolesData.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="fas fa-info-circle"></i> No hay roles registrados
                    </td>
                </tr>
            `);
            return;
        }
        
        rolesData.forEach(function(role) {
            let permisosHtml = '';
            if (role.permissions && role.permissions.length > 0) {
                role.permissions.forEach(function(permiso) {
                    permisosHtml += `<span class="badge badge-info badge-permission">${permiso}</span> `;
                });
            } else {
                permisosHtml = '<span class="text-muted">Sin permisos asignados</span>';
            }

            let actionsHtml = `
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-warning" onclick="editRole(${role.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger" onclick="deleteRole(${role.id}, '${role.name}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;

            tbody.append(`
                <tr>
                    <td>${role.id}</td>
                    <td><strong>${role.name}</strong></td>
                    <td>${permisosHtml}</td>
                    <td><span class="badge badge-primary">${role.permissions_count}</span></td>
                    <td><span class="badge badge-secondary">${role.users_count}</span></td>
                    <td>${actionsHtml}</td>
                </tr>
            `);
        });
    }

    function showCreateModal() {
        $('#roleModalTitle').text('Crear Nuevo Rol');
        $('#roleForm')[0].reset();
        $('#role_id').val('');
        $('#saveRoleBtn').html('<i class="fas fa-save"></i> Crear Rol');
        
        loadPermissionsInModal();
        updateSelectedPermissionsSummary();
        $('#roleModal').modal('show');
    }

    function loadPermissionsInModal(selectedPermissions = []) {
        const container = $('#permissions-container');
        container.empty();
        
        if (permissionsData.length === 0) {
            container.append('<p class="text-muted">No hay permisos disponibles</p>');
            return;
        }
        
        // Agrupar en columnas
        const itemsPerColumn = Math.ceil(permissionsData.length / 3);
        let currentColumn = 0;
        let currentColumnDiv = $('<div class="col-md-4"></div>');
        
        permissionsData.forEach(function(permission, index) {
            if (index > 0 && index % itemsPerColumn === 0) {
                container.append(currentColumnDiv);
                currentColumn++;
                currentColumnDiv = $('<div class="col-md-4"></div>');
            }
            
            const isChecked = selectedPermissions.includes(permission) ? 'checked' : '';
            const checkboxHtml = `
                <div class="form-check permission-checkbox">
                    <input class="form-check-input" type="checkbox" name="permissions[]" value="${permission}" 
                           id="permission_${index}" ${isChecked}>
                    <label class="form-check-label permission-label" for="permission_${index}">
                        ${permission}
                    </label>
                </div>
            `;
            currentColumnDiv.append(checkboxHtml);
        });
        
        // Agregar la última columna
        container.append(currentColumnDiv);
    }

    function editRole(roleId) {
        const role = rolesData.find(r => r.id === roleId);
        if (!role) {
            showNotification('Rol no encontrado', 'error');
            return;
        }
        
        $('#roleModalTitle').text('Editar Rol: ' + role.name);
        $('#role_id').val(role.id);
        $('#role_name').val(role.name);
        $('#saveRoleBtn').html('<i class="fas fa-edit"></i> Actualizar Rol');
        
        loadPermissionsInModal(role.permissions);
        updateSelectedPermissionsSummary();
        $('#roleModal').modal('show');
    }

    function saveRole() {
        const roleId = $('#role_id').val();
        const formData = {
            name: $('#role_name').val(),
            permissions: []
        };
        
        // Recoger permisos seleccionados
        $('input[name="permissions[]"]:checked').each(function() {
            formData.permissions.push($(this).val());
        });
        
        if (!formData.name.trim()) {
            showNotification('El nombre del rol es requerido', 'error');
            return;
        }
        
        const url = roleId ? `/admin/api/roles-spatie/${roleId}` : '/admin/api/roles-spatie';
        const method = roleId ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            type: method,
            data: JSON.stringify(formData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#roleModal').modal('hide');
                    showNotification(response.message, 'success');
                    loadRoles();
                } else {
                    showNotification(response.message || 'Error al guardar el rol', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                let errorMessage = 'Error al guardar el rol';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    } else if (errorResponse.errors) {
                        errorMessage = Object.values(errorResponse.errors).flat().join(', ');
                    }
                } catch (e) {
                    // Si no se puede parsear, usar mensaje por defecto
                }
                
                showNotification(errorMessage, 'error');
            }
        });
    }

    function deleteRole(roleId, roleName) {
        if (confirm(`¿Estás seguro de eliminar el rol "${roleName}"?\n\nEsta acción no se puede deshacer.`)) {
            $.ajax({
                url: `/admin/api/roles-spatie/${roleId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        loadRoles();
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr.responseText);
                    let errorMessage = 'Error al eliminar el rol';
                    
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {
                        // Si no se puede parsear, usar mensaje por defecto
                    }
                    
                    showNotification(errorMessage, 'error');
                }
            });
        }
    }

    function updateSelectedPermissionsSummary() {
        const selectedPermissions = [];
        $('input[name="permissions[]"]:checked').each(function() {
            selectedPermissions.push($(this).val());
        });

        if (selectedPermissions.length > 0) {
            let html = '';
            selectedPermissions.forEach(function(permission) {
                html += `<span class="badge badge-success badge-permission">${permission}</span> `;
            });
            $('#selected-permissions-list').html(html);
            $('#selected-permissions-summary').show();
        } else {
            $('#selected-permissions-summary').hide();
        }
    }

    function showNotification(message, type) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
        
        const notification = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="${iconClass}"></i> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('body').append(notification);
        
        // Auto-remover después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }
</script>
@endsection