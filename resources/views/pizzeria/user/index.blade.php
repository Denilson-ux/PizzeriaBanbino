@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1>Usuarios</h1>
@stop

@section('content')

{{-- Loader --}}
<div id="loader-container"></div>

<!-- BARRA DE NAVEGACION -->
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#usuario-tab" data-toggle="tab"><i class="fas fa-user"></i>&nbsp;&nbsp;Usuarios</a></li>
            <li class="nav-item"><a class="nav-link" href="#usuario-eliminados-tab" data-toggle="tab"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Eliminados</a></li>
        </ul>
    </div>
</div>

<div class="card-body">
    <div class="tab-content">
        <!------------------- INDEX ------------------->
        <div class="active tab-pane" id="usuario-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div id="toolbar">
                        <a id="btn-nuevo-user" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo
                        </a>
                    </div>
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-user" data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" data-toolbar="#toolbar"
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id">ID</th>
                                <th data-sortable="true" data-field="name">Nombre</th>
                                <th data-sortable="true" data-field="email">Correo</th>
                                <th data-sortable="true" data-field="persona_info" data-formatter="formatPersonaInfo">Tipo de Persona</th>
                                <th data-sortable="true" data-width="200" data-field="imagen_td">Imagen</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!------------------- ELIMINADOS ------------------->
        <div class="tab-pane" id="usuario-eliminados-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-user-eliminados" 
                        data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" 
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id">ID</th>
                                <th data-sortable="true" data-field="name">Nombre</th>
                                <th data-sortable="true" data-field="email">Correo</th>
                                <th data-sortable="true" data-field="persona_info" data-formatter="formatPersonaInfo">Tipo de Persona</th>
                                <th data-sortable="true" data-width="200" data-field="imagen_td">Imagen</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> 
        </div>
    </div>
</div>

{{-- Modal nuevo usuario --}}
<div class="modal fade" id="modal-nuevo-user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Nuevo usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nombre de usuario:</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="juanenrique" required>

                        <label for="email" class="form-label">Correo:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@gmail.com" required>
                        <p for="email-advertencia" id="email-advertencia" class="form-label text-danger d-none">Correo no disponible</p>

                        <label for="password" class="form-label">Contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" aria-describedby="toggle-password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password" title="Mostrar/ocultar contraseña">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>

                        <label for="password-repite" class="form-label">Repite la contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password-repite" name="password-repite" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password-repite" title="Mostrar/ocultar contraseña">
                                <i class="far fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Selector de tipo de persona -->
                        <label for="tipo_persona" class="form-label">Tipo de Persona:</label>
                        <select class="form-control" id="tipo_persona" name="tipo_persona" required>
                            <option value="">Selecciona el tipo...</option>
                            <option value="cliente">Cliente</option>
                            <option value="empleado">Empleado</option>
                            <option value="repartidor">Repartidor</option>
                        </select>

                        <!-- Selector dinámico de persona según tipo -->
                        <label for="id_persona" class="form-label">Persona:</label>
                        <select id="id_persona" name="id_persona" class="form-control" required disabled>
                            <option value="">Primero selecciona el tipo...</option>
                        </select>

                        <label for="imagen" class="form-label">Imagen:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/*" onchange="mostrarVistaPrevia()">
                            <label class="custom-file-label" for="imagen">Seleccionar imagen</label>
                        </div>
                        <div class="mt-3">
                            <img id="vista-previa" src="#" alt="Vista previa de la imagen" style="max-width: 100%; max-height: 200px; display: none;">
                        </div>

                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Nota:</strong> Los roles se asignan desde <strong>"Asignación Roles-Permisos"</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                    <i class="fas fa-door-open"></i> Close
                </button>
                <button id="guardar-user" type="button" class="btn btn-success">
                    <i class="far fa-save"></i> Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- modal edit usuario-->
<div class="modal fade" id="modal-edit-user" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Editar usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name-edit" class="form-label">Nombre de usuario:</label>
                    <input type="text" class="form-control" id="name-edit" name="name-edit" placeholder="juanenrique" required>

                    <label for="email-edit" class="form-label">Correo:</label>
                    <input type="email-edit" class="form-control" id="email-edit" name="email-edit" placeholder="ejemplo@gmail.com" required>
                    <p for="email-advertencia-edit" id="email-advertencia-edit" class="form-label text-danger d-none">Correo no disponible</p>

                    <label for="password-edit" class="form-label">Contraseña:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password-edit" name="password-edit" aria-describedby="toggle-password-edit" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password-edit" title="Mostrar/ocultar contraseña">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>

                    <label for="password-repite-edit" class="form-label">Repite la contraseña:</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password-repite-edit" name="password-repite-edit" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggle-password-repite-edit" title="Mostrar/ocultar contraseña">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Selector de tipo de persona EDIT -->
                    <label for="tipo_persona_edit" class="form-label">Tipo de Persona:</label>
                    <select class="form-control" id="tipo_persona_edit" name="tipo_persona_edit" required>
                        <option value="">Selecciona el tipo...</option>
                        <option value="cliente">Cliente</option>
                        <option value="empleado">Empleado</option>
                        <option value="repartidor">Repartidor</option>
                    </select>

                    <!-- Selector dinámico de persona EDIT -->
                    <label for="id_persona_edit" class="form-label">Persona:</label>
                    <select id="id_persona_edit" name="id_persona_edit" class="form-control" required disabled>
                        <option value="">Primero selecciona el tipo...</option>
                    </select>

                    <label for="imagen-edit" class="form-label">Imagen:</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="imagen-edit" name="imagen-edit" accept="image/*" onchange="mostrarVistaPreviaEdit()">
                        <label class="custom-file-label" for="imagen-edit">Seleccionar imagen</label>
                    </div>
                    <div class="mt-3">
                        <img id="vista-previa-edit" src="#" alt="Vista previa de la imagen" style="max-width: 100%; max-height: 200px; display: none;">
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Nota:</strong> Para cambiar roles, usa <strong>"Asignación Roles-Permisos"</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                <i class="fas fa-door-open"></i> Close
            </button>
            <button id="actualizar-user" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Actualizar
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
    <link rel="stylesheet" href="{{asset('/bootstrap/css/select2.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('/bootstrap/css/chosen.css')}}"/>
    <style>
        .persona-badge { display:inline-block; margin:2px; }
        .persona-badge .badge { font-size:11px; padding:4px 8px; }
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
    <script src="{{asset('/bootstrap/js/select2.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/chosen.jquery.js')}}"></script>
    <script src="{{asset('/bootstrap/js/chosen.jquery.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/spin.min.js')}}"></script>
    <script src="{{asset('/pizzeria/js/parametros.js')}}"></script>

    <script>
    // Formatter para mostrar persona con tipo
    function formatPersonaInfo(value, row) {
        if (!row.persona_info || row.persona_info === 'Sin persona asignada') {
            return '<span class="badge badge-secondary">Sin persona asignada</span>';
        }
        return '<span class="badge badge-info">' + row.persona_info + '</span>';
    }
    window.formatPersonaInfo = formatPersonaInfo;

    // SISTEMA CORRECTO: Eventos para cargar personas por tipo
    $(document).ready(function() {
        console.log('Sistema de personas iniciado correctamente');
        
        // Evento para modal nuevo - tipo de persona
        $('#tipo_persona').on('change', function() {
            var tipoSeleccionado = $(this).val();
            console.log('Tipo persona seleccionado (NUEVO):', tipoSeleccionado);
            cargarPersonasPorTipo(tipoSeleccionado, '#id_persona', '[NUEVO] ');
        });

        // Evento para modal editar - tipo de persona  
        $('#tipo_persona_edit').on('change', function() {
            var tipoSeleccionado = $(this).val();
            console.log('Tipo persona seleccionado (EDIT):', tipoSeleccionado);
            cargarPersonasPorTipo(tipoSeleccionado, '#id_persona_edit', '[EDIT] ');
        });
    });

    // Función para cargar personas según tipo seleccionado
    function cargarPersonasPorTipo(tipoPersona, selectPersona, debugPrefix) {
        debugPrefix = debugPrefix || '';
        console.log(debugPrefix + 'Cargando personas para tipo:', tipoPersona);
        
        if (!tipoPersona) {
            $(selectPersona).prop('disabled', true).html('<option value="">Primero selecciona el tipo...</option>');
            return;
        }

        $(selectPersona).prop('disabled', false).html('<option value="">Cargando...</option>');
        
        var url = '/admin/personas?tipo=' + tipoPersona;
        console.log(debugPrefix + 'Realizando petición a:', url);
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log(debugPrefix + 'Respuesta exitosa:', response);
                
                if (!response || !Array.isArray(response)) {
                    console.error(debugPrefix + 'Respuesta no es un array válido:', response);
                    $(selectPersona).html('<option value="">Error: respuesta inválida</option>');
                    return;
                }
                
                var options = '<option value="">Selecciona una persona...</option>';
                for (var i = 0; i < response.length; i++) {
                    var persona = response[i];
                    options += '<option value="' + persona.id + '">' + persona.nombre_completo + '</option>';
                }
                
                $(selectPersona).html(options);
                console.log(debugPrefix + 'Select actualizado con', response.length, 'opciones');
            },
            error: function(xhr, status, error) {
                console.error(debugPrefix + 'Error en petición AJAX:', {
                    xhr: xhr,
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });
                $(selectPersona).html('<option value="">Error al cargar personas</option>');
            }
        });
    }
    </script>
    <script src="{{asset('/pizzeria/js/user.js')}}"></script>
@stop