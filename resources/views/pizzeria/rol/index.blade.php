@extends('adminlte::page')

@section('title', 'Roles y Permisos')

@section('content_header')
    <h1>Gestión de Roles y Permisos</h1>
@stop

@section('content')

<div id="loader-container"></div>

<!-- CONTENIDO PRINCIPAL - SOLO ROLES CON PERMISOS -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-user-shield"></i> Roles con Permisos</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div id="toolbar-roles-spatie" class="mb-3">
                    <button id="btn-nuevo-rol-spatie" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nuevo Rol con Permisos
                    </button>
                    <button id="btn-recargar-roles" class="btn btn-info ml-2">
                        <i class="fas fa-sync"></i> Recargar
                    </button>
                </div>
                <table 
                    class="table table-bordered table-hover table-striped"
                    id="tabla-roles-spatie" 
                    data-show-export="true" 
                    data-search="true"
                    data-show-print="true" 
                    data-toggle="table" 
                    data-toolbar="#toolbar-roles-spatie"
                    data-height="500" 
                    data-pagination="true" 
                    data-show-columns="true">
                    <thead>
                        <tr>
                            <th data-sortable="true" data-field="id">ID</th>
                            <th data-sortable="true" data-field="name">Nombre del Rol</th>
                            <th data-sortable="false" data-field="permissions">Permisos</th>
                            <th data-sortable="true" data-field="permissions_count">N° Permisos</th>
                            <th data-sortable="true" data-field="users_count">Usuarios</th>
                            <th data-sortable="false" data-field="acciones" data-width="150">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal CREAR/EDITAR rol con permisos (Spatie) -->
<div class="modal fade" id="modal-rol-spatie" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titulo-modal-rol">Crear Nuevo Rol con Permisos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rol-id" value="">
                <div class="row">
                    <div class="col-md-4">
                        <label for="nombre-rol" class="form-label">Nombre del Rol:</label>
                        <input type="text" class="form-control" id="nombre-rol" name="nombre-rol" placeholder="Ejemplo: Editor" required>
                        <small class="form-text text-muted">El nombre debe ser único</small>
                        
                        <div class="alert alert-info mt-3">
                            <strong><i class="fas fa-info-circle"></i> Información:</strong>
                            <br>Los usuarios se asignan a estos roles desde la sección <strong>"Asignación Roles-Permisos"</strong>.
                        </div>
                        
                        <div class="mt-3">
                            <strong>Seleccionados: </strong><span id="contador-permisos" class="badge badge-primary">0</span>
                            <div id="preview-permisos" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Permisos del Rol:</label>
                        <div class="card">
                            <div class="card-body" style="max-height: 420px; overflow-y: auto;">
                                <div class="row" id="permisos-container-spatie"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button id="btn-guardar-rol-spatie" type="button" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
    <link rel="stylesheet" href="{{asset('/pizzeria/css/style.css')}}"/>
    <link rel="stylesheet" href="{{asset('/bootstrap/css/bootstrap-table.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{asset('/bootstrap/css/alertify.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/bootstrap/css/default.min.css')}}"/>
    <style>
        .badge-perm { 
            font-size: 11px; 
            margin: 2px; 
            padding: 4px 8px; 
        }
        .card-header.bg-primary {
            background: linear-gradient(45deg, #007bff, #0056b3) !important;
        }
        .alert-info {
            background-color: #e3f2fd;
            border-color: #2196f3;
            color: #0d47a1;
        }
    </style>
@stop

@section('js')
    <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/bootstrap-table-print.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/tableExport.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/jspdf.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/jspdf.plugin.autotable.js')}}"></script> 
    <script src="{{asset('/bootstrap/js/bootstrap-table-export.min.js')}}"></script> 
    <script src="{{asset('/bootstrap/js/alertify.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/spin.min.js')}}"></script>

<script>
$(function(){
    let permisos = [];
    let roles = [];

    // Cargar al inicio
    cargarPermisos();
    cargarRoles();

    // Botones
    $('#btn-recargar-roles').on('click', cargarRoles);
    $('#btn-nuevo-rol-spatie').on('click', function(){
        $('#titulo-modal-rol').text('Crear Nuevo Rol con Permisos');
        $('#rol-id').val('');
        $('#nombre-rol').val('');
        pintarPermisos([]);
        actualizarPreview();
        $('#btn-guardar-rol-spatie').data('modo','crear');
        $('#modal-rol-spatie').modal('show');
    });

    $('#btn-guardar-rol-spatie').on('click', function(){
        const modo = $(this).data('modo');
        if (modo === 'editar') { enviarGuardar(true); } else { enviarGuardar(false); }
    });

    // Listeners para checkboxes
    $(document).on('change', 'input[name="permissions_spatie[]"]', actualizarPreview);

    // Funciones
    function cargarPermisos(){
        $.get('/admin/permisos-spatie')
         .done(res => {
            if(res.success){ permisos = res.data; }
            else { alertify.error(res.message || 'No se pudieron cargar permisos'); }
         })
         .fail(()=> alertify.error('Error de conexión al cargar permisos'));
    }

    function cargarRoles(){
        $.get('/admin/roles-spatie')
         .done(res => {
            if(res.success){ roles = res.data; renderTablaRoles(); }
            else { alertify.error(res.message || 'No se pudieron cargar roles'); }
         })
         .fail(()=> alertify.error('Error de conexión al cargar roles'));
    }

    function renderTablaRoles(){
        const data = roles.map(r => ({
            id: r.id,
            name: r.name,
            permissions: r.permissions && r.permissions.length
                ? r.permissions.map(p=>`<span class="badge badge-info badge-perm">${p}</span>`).join(' ')
                : '<span class="text-muted">Sin permisos</span>',
            permissions_count: `<span class="badge badge-primary">${r.permissions_count}</span>`,
            users_count: `<span class="badge badge-secondary">${r.users_count}</span>`,
            acciones: `
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-warning" data-id="${r.id}" onclick="editar(${r.id})" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger" data-id="${r.id}" onclick="eliminar(${r.id}, '${r.name.replace(/'/g, "\'")}')" title="Eliminar"><i class="fas fa-trash"></i></button>
                </div>`
        }));
        $('#tabla-roles-spatie').bootstrapTable('load', data);
    }

    window.editar = function(id){
        const r = roles.find(x=>x.id===id);
        if(!r){ return alertify.error('Rol no encontrado'); }
        $('#titulo-modal-rol').text('Editar Rol: '+r.name);
        $('#rol-id').val(r.id);
        $('#nombre-rol').val(r.name);
        pintarPermisos(r.permissions || []);
        actualizarPreview();
        $('#btn-guardar-rol-spatie').data('modo','editar');
        $('#modal-rol-spatie').modal('show');
    }

    window.eliminar = function(id, nombre){
        alertify.confirm(`¿Eliminar el rol "${nombre}"?`, 'Esta acción eliminará el rol y sus asignaciones', function(){
            $.ajax({
                url: `/admin/roles-spatie/${id}`,
                type: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
            }).done(res=>{
                if(res.success){ 
                    alertify.success(res.message); 
                    cargarRoles(); 
                }
                else { alertify.error(res.message || 'No se pudo eliminar'); }
            }).fail(()=> alertify.error('Error al eliminar'));
        });
    }

    function pintarPermisos(seleccionados){
        const cont = $('#permisos-container-spatie');
        cont.empty();
        if(!permisos || !permisos.length){
            cont.append('<div class="col-12 text-muted">No hay permisos disponibles</div>');
            return;
        }
        const porCol = Math.ceil(permisos.length/3);
        let col = $('<div class="col-md-4"></div>');
        permisos.forEach((p,i)=>{
            if(i>0 && i%porCol===0){ cont.append(col); col=$('<div class="col-md-4"></div>'); }
            const checked = seleccionados.includes(p) ? 'checked' : '';
            col.append(`
                <div class="form-check mb-1">
                    <input class="form-check-input" type="checkbox" name="permissions_spatie[]" value="${p}" id="perm_${i}" ${checked}>
                    <label class="form-check-label" for="perm_${i}">${p}</label>
                </div>`);
        });
        cont.append(col);
    }

    function actualizarPreview(){
        const sel = [];
        $('input[name="permissions_spatie[]"]:checked').each(function(){ sel.push($(this).val()); });
        $('#contador-permisos').text(sel.length);
        $('#preview-permisos').html(sel.length ? sel.map(p=>`<span class="badge badge-success badge-perm">${p}</span>`).join(' ') : '<span class="text-muted">Ningún permiso seleccionado</span>');
    }

    function enviarGuardar(esEdicion){
        const id = $('#rol-id').val();
        const name = $('#nombre-rol').val().trim();
        if(!name){ return alertify.error('El nombre del rol es obligatorio'); }
        const permisosSel = [];
        $('input[name="permissions_spatie[]"]:checked').each(function(){ permisosSel.push($(this).val()); });

        const url = esEdicion ? `/admin/roles-spatie/${id}` : '/admin/roles-spatie';
        const data = { name, permissions: permisosSel, _token: '{{ csrf_token() }}' };
        if(esEdicion){ data._method = 'PUT'; }

        $.post(url, data)
         .done(res=>{
            if(res.success){
                alertify.success(res.message);
                $('#modal-rol-spatie').modal('hide');
                cargarRoles();
            }else{
                alertify.error(res.message || 'No se pudo guardar');
            }
         })
         .fail(xhr=>{
            let msg = 'Error al guardar';
            try{ const r = JSON.parse(xhr.responseText); if(r.errors) msg = Object.values(r.errors).flat().join(', '); else if(r.message) msg=r.message; }catch(e){}
            alertify.error(msg);
         });
    }
});
</script>
@stop