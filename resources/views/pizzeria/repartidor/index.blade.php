@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Repartidores</h1>
@stop

@section('content')

{{-- Loader --}}
<div id="loader-container"></div>

<!-- BAARRA DE NAVEGACION -->
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#repartidor-tab"
                    data-toggle="tab"><i class="fas fa-user"></i>&nbsp;&nbsp;Clientes</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#repartidor-eliminados-tab"
                    data-toggle="tab"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Eliminados</a>
            </li>
        </ul>
    </div>
</div>


<div class="card-body">

    <div class="tab-content">
        <!------------------- INDEX ------------------->
        <div class="active tab-pane" id="repartidor-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div id="toolbar">
                        <a id="btn-nuevo-repartidor" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo
                        </a>
                    </div>
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-repartidor" data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" data-toolbar="#toolbar"
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_repartidor">ID</th>
                                <th data-sortable="true" data-field="nombre">Nombre</th>
                                <th data-sortable="true" data-field="paterno">Paterno</th>
                                <th data-sortable="true" data-field="materno">Materno</th>
                                <th data-sortable="true" data-field="telefono">Telefono</th>
                                <th data-sortable="true" data-field="correo">Correo</th>
                                <th data-sortable="true" data-field="licencia_conducir">Nro. Licencia</th>
                                <th data-sortable="true" data-field="imagen_td">Imagen</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!------------------- ELIMINADOS ------------------->
        <div class="tab-pane" id="repartidor-eliminados-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-repartidor-eliminados" 
                        data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" 
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_repartidor">ID</th>
                                <th data-sortable="true" data-field="nombre">Nombre</th>
                                <th data-sortable="true" data-field="paterno">Paterno</th>
                                <th data-sortable="true" data-field="materno">Materno</th>
                                <th data-sortable="true" data-field="telefono">Telefono</th>
                                <th data-sortable="true" data-field="correo">Correo</th>
                                <th data-sortable="true" data-field="licencia_conducir">Nro. Licencia</th>
                                <th data-sortable="true" data-field="imagen_td">Imagen</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> 
        </div>
    </div>

</div>


<!-- modal nuevo repartidor-->

<div class="modal fade" id="modal-nuevo-repartidor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Nuevo Empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="form-group row">
                        <label for="nombre" class="col-sm-4 col-form-label">Nombre:</label>
                        <div class="col-sm-8">
                            <input name="nombre" type="text" class="form-control" id="nombre" placeholder="Ej: Juan">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="paterno" class="col-sm-4 col-form-label">Paterno:</label>
                        <div class="col-sm-8">
                            <input name="paterno" type="text" class="form-control" id="paterno" placeholder="Ej: Perez">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="materno" class="col-sm-4 col-form-label">Materno:</label>
                        <div class="col-sm-8">
                            <input name="materno" type="text" class="form-control" id="materno" placeholder="Ej: Ramos">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="telefono" class="col-sm-4 col-form-label">Teléfono:</label>
                        <div class="col-sm-8">
                            <input name="telefono" type="text" class="form-control" id="telefono" placeholder="Ej: 69041234">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="correo" class="col-sm-4 col-form-label">Correo:</label>
                        <div class="col-sm-8">
                            <input name="correo" type="mail" class="form-control" id="correo" placeholder="Ej: ejemplo@gmail.com">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="licencia_conducir" class="col-sm-4 col-form-label">Nro. Licencia:</label>
                        <div class="col-sm-8">
                            <input name="licencia_conducir" type="text" class="form-control" id="licencia_conducir" placeholder="Ej: 1304" value="0">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="imagen" class="col-sm-4 col-form-label">Imagen:</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="imagen" accept="image/*" onchange="mostrarVistaPrevia()">
                                    <label class="custom-file-label" for="imagen">Seleccionar imagen</label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <img id="vista-previa" src="#" alt="Vista previa de la imagen" style="max-width: 100%; max-height: 200px; display: none;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                <i class="fas fa-door-open"></i>    
                Close
            </button>
            <button id="guardar-repartidor" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Guardar
            </button>
        </div>
      </div>
    </div>
  </div>


  <!-- modal edit repartidor-->

  <div class="modal fade" id="modal-edit-repartidor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Editar Empleado</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="form-group row">
                        <label for="nombre-edit" class="col-sm-4 col-form-label">Nombre:</label>
                        <div class="col-sm-8">
                            <input name="nombre-edit" type="text" class="form-control" id="nombre-edit" placeholder="Ej: Juan">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="paterno-edit" class="col-sm-4 col-form-label">Paterno:</label>
                        <div class="col-sm-8">
                            <input name="paterno-edit" type="text" class="form-control" id="paterno-edit" placeholder="Ej: Perez">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="materno-edit" class="col-sm-4 col-form-label">Materno:</label>
                        <div class="col-sm-8">
                            <input name="materno-edit" type="text" class="form-control" id="materno-edit" placeholder="Ej: Ramos">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="telefono-edit" class="col-sm-4 col-form-label">Teléfono:</label>
                        <div class="col-sm-8">
                            <input name="telefono-edit" type="text" class="form-control" id="telefono-edit" placeholder="Ej: 69041234">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="correo-edit" class="col-sm-4 col-form-label">Correo:</label>
                        <div class="col-sm-8">
                            <input name="correo-edit" type="mail" class="form-control" id="correo-edit" placeholder="Ej: ejemplo@gmail.com">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="licencia_conducir-edit" class="col-sm-4 col-form-label">Nro. Licencia:</label>
                        <div class="col-sm-8">
                            <input name="licencia_conducir-edit" type="text" class="form-control" id="licencia_conducir-edit" placeholder="Ej: 10">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="imagen-edit" class="col-sm-4 col-form-label">Imagen:</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="imagen-edit" accept="image/*" onchange="mostrarVistaPreviaEdit()">
                                    <label class="custom-file-label" for="imagen-edit">Seleccionar imagen</label>
                                </div>
                            </div>
                            <div class="mt-3">
                                <img id="vista-previa-edit" src="#" alt="Vista previa de la imagen" style="max-width: 100%; max-height: 200px; display: none;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                <i class="fas fa-door-open"></i>    
                Close
            </button>
            <button id="actualizar-repartidor" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Actualizar
            </button>
        </div>
      </div>
    </div>
  </div>



  @stop

@section('css')

    {{-- stilos personalizados --}}
    <link rel="stylesheet" href="{{asset('/pizzeria/css/style.css')}}"/>

    {{-- <link rel="stylesheet" href="{{asset('/bootstrap/css/font-awesome.all.min.css')}}"/> --}}
    <link rel="stylesheet" href="{{asset('/bootstrap/css/bootstrap-table.min.css')}}">
    {{-- <link rel="stylesheet" href="{{asset('/bootstrap/css/bootstrap-icons.css')}}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    {{-- Alert --}}
    <link rel="stylesheet" href="{{asset('/bootstrap/css/alertify.min.css')}}"/>
    <!-- Default theme -->
    <link rel="stylesheet" href="{{asset('/bootstrap/css/default.min.css')}}"/>


@stop

@section('js')
    {{-- bootstrap table --}}
    <script src="{{asset('/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/bootstrap-table.min.js')}}"></script>
    {{-- para imprimir --}}
    <script src="{{asset('/bootstrap/js/bootstrap-table-print.min.js')}}"></script>
    {{-- para exportar --}}
    <script src="{{asset('/bootstrap/js/tableExport.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/jspdf.min.js')}}"></script>
    <script src="{{asset('/bootstrap/js/jspdf.plugin.autotable.js')}}"></script> 
    <script src="{{asset('/bootstrap/js/bootstrap-table-export.min.js')}}"></script> 

    {{-- alert --}}
    <script src="{{asset('/bootstrap/js/alertify.min.js')}}"></script>
    
    {{-- loader --}}
    <script src="{{asset('/bootstrap/js/spin.min.js')}}"></script>


    <script src="{{asset('/pizzeria/js/parametros.js')}}"></script>
    <script src="{{asset('/pizzeria/js/repartidor.js')}}"></script>
@stop