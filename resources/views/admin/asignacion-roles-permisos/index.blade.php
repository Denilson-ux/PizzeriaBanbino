@extends('adminlte::page')

@section('title', 'Asignación Roles y Permisos')

@section('content_header')
    <h1>Asignación de Roles y Permisos a Usuarios</h1>
@stop

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-tag"></i> Asignación de Roles a Usuarios
                    </h3>
                    <div class="card-tools">
                        <button class="btn btn-primary" onclick="showAssignModal()">
                            <i class="fas fa-user-plus"></i> Asignar Roles a Usuario
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Información:</strong> Aquí puedes asignar los roles creados en la sección "Roles" a los usuarios. Los permisos se heredan automáticamente del rol asignado.
                    </div>
                    
                    <div class="table-responsive">
                        <table id="users-table" class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="15%">Usuario</th>
                                    <th width="20%">Correo</th>
                                    <th width="25%">Roles Asignados</th>
                                    <th width="20%">Permisos Efectivos</th>
                                    <th width="15%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="users-tbody">
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->persona)
                                            <br><small class="text-muted">{{ $user->persona->nombre }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->roles->count() > 0)
                                            @foreach($user->roles as $role)
                                                <span class="badge badge-primary">{{ $role->name }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Sin roles</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php $allPermissions = $user->getAllPermissions(); @endphp
                                        @if($allPermissions->count() > 0)
                                            <span class="badge badge-success">{{ $allPermissions->count() }} permisos</span>
                                            <button class="btn btn-sm btn-outline-info" onclick="showUserPermissions({{ $user->id }})">
                                                <i class="fas fa-eye"></i> Ver
                                            </button>
                                        @else
                                            <span class="text-muted">Sin permisos</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-warning" onclick="editUserAssignment({{ $user->id }})" title="Editar asignaciones">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="removeAllAssignments({{ $user->id }}, '{{ $user->name }}')" title="Remover todas las asignaciones">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para asignar roles a usuario -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="assignModalTitle">Asignar Roles a Usuario</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="assignForm">
                <div class="modal-body">
                    <input type="hidden" id="assign_user_id" name="user_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="select_user">Usuario *</label>
                                <select class="form-control" id="select_user" name="user_id" required>
                                    <option value="">Seleccionar usuario...</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Roles Disponibles</label>
                                <div class="card">
                                    <div class="card-body" style="max-height:200px;overflow-y:auto;">
                                        @foreach($roles as $role)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}">
                                            <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }} <small class="text-muted">({{ $role->permissions->count() }} permisos)</small></label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vista Previa de Permisos</label>
                                <div id="permissions-preview" class="card">
                                    <div class="card-body">
                                        <p class="text-muted">Selecciona roles para ver los permisos que se asignarán</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-user-check"></i> Asignar Roles</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para mostrar permisos de usuario -->
<div class="modal fade" id="permissionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="permissionsModalTitle">Permisos del Usuario</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="permissionsModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
let rolesData = @json($roles);

function showAssignModal(){
  $('#assignModalTitle').text('Asignar Roles a Usuario');
  $('#assignForm')[0].reset();
  $('#select_user').prop('disabled', false);
  $('#assign_user_id').val('');
  $('#assignModal').modal('show');
}
function editUserAssignment(userId){
  $('#assignModalTitle').text('Editar Asignaciones');
  $('#assignForm')[0].reset();
  $('#select_user').val(userId).prop('disabled', true);
  $('#assign_user_id').val(userId);
  $('#assignModal').modal('show');
}
$('#assignForm').on('submit', function(e){
  e.preventDefault();
  const uid = $('#select_user').val();
  if (!uid) { alert('Debes seleccionar un usuario'); return; }
  $('#assign_user_id').val(uid);
  const formData = new FormData(this);
  $.ajax({
    url: '/admin/asignacion-roles-permisos',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
    success: function(){ $('#assignModal').modal('hide'); location.reload(); },
    error: function(xhr){ let msg='Error al asignar roles'; try{ const r=JSON.parse(xhr.responseText); if(r.errors) msg=Object.values(r.errors).flat().join(', '); else if(r.message) msg=r.message; }catch(e){} alert(msg);} 
  });
});
function removeAllAssignments(userId, name){
  if(!confirm(`¿Remover TODAS las asignaciones de "${name}"?`)) return;
  // Enviar como POST con _method=DELETE para máxima compatibilidad
  const formData = new FormData();
  formData.append('_method','DELETE');
  formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
  $.ajax({
    url: `/admin/asignacion-roles-permisos/${userId}`,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(){ location.reload(); },
    error: function(xhr){ let msg='Error al remover asignaciones'; try{ const r=JSON.parse(xhr.responseText); if(r.message) msg=r.message; }catch(e){} alert(msg);} 
  });
}
function showUserPermissions(userId){
  $.get(`/admin/users/${userId}/assignments`, function(response){
    let html = '';
    html += '<h6>Roles:</h6>';
    if(response.roles && response.roles.length){ response.roles.forEach(r=> html += `<span class="badge badge-primary mr-1">${r.name}</span>`); }
    else { html += '<span class="text-muted">Sin roles</span>'; }
    html += '<hr><h6>Permisos directos:</h6>';
    if(response.permissions && response.permissions.length){ response.permissions.forEach(p=> html += `<span class="badge badge-info mr-1">${p.name}</span>`); }
    else { html += '<span class="text-muted">Sin permisos directos</span>'; }
    $('#permissionsModalTitle').text('Permisos del Usuario');
    $('#permissionsModalBody').html(html);
    $('#permissionsModal').modal('show');
  });
}
</script>
@endsection