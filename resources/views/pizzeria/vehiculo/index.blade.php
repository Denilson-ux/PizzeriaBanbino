@extends('adminlte::page')

@section('title', 'Vehiculos')

@section('content_header')
    <h1>Vehiculos</h1>
@stop

@section('content')

{{-- Loader --}}
<div id="loader-container"></div>

{{-- Loader --}}
<div id="loader-container"></div>

<!-- BAARRA DE NAVEGACION -->
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#vehiculo-tab"
                    data-toggle="tab"><i class="fas fa-user"></i>&nbsp;&nbsp;vehiculos</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#vehiculo-eliminados-tab"
                    data-toggle="tab"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Eliminados</a>
            </li>
        </ul>
    </div>
</div>


<div class="card-body">

    <div class="tab-content">
        <!------------------- INDEX ------------------->
        <div class="active tab-pane" id="vehiculo-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div id="toolbar">
                        <a id="btn-nuevo-vehiculo" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo
                        </a>
                    </div>
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-vehiculo" data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" data-toolbar="#toolbar"
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_vehiculo">ID</th>
                                <th data-sortable="true" data-field="placa">Placa</th>
                                <th data-sortable="true" data-field="marca">Marca</th>
                                <th data-sortable="true" data-field="modelo">Modelo</th>
                                <th data-sortable="true" data-field="color">Color</th>
                                <th data-sortable="true" data-field="anio">Año</th>
                                <th data-sortable="true" data-field="tipo_vehiculo">Tipo de Vehiculo</th>
                                <th data-sortable="true" data-width="200" data-field="imagen_td">Imagen</th>
                                <th data-sortable="true" data-width="200" data-field="repartidor">Conductor</th>
                                <th data-sortable="true" data-width="200" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!------------------- ELIMINADOS ------------------->
        <div class="tab-pane" id="vehiculo-eliminados-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-vehiculo-eliminados" 
                        data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" 
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_vehiculo">ID</th>
                                <th data-sortable="true" data-field="placa">Placa</th>
                                <th data-sortable="true" data-field="marca">Marca</th>
                                <th data-sortable="true" data-field="modelo">Modelo</th>
                                <th data-sortable="true" data-field="color">Color</th>
                                <th data-sortable="true" data-field="anio">Año</th>
                                <th data-sortable="true" data-field="tipo_vehiculo">Tipo de Vehiculo</th>
                                <th data-sortable="true" data-width="200" data-field="imagen_td">Imagen</th>
                                <th data-sortable="true" data-width="200" data-field="repartidor">Conductor</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> 
        </div>
    </div>

</div>


<div class="modal fade" id="modal-nuevo-vehiculo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Nuevo vehiculo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="form-group row">
                        <label for="placa" class="col-sm-4 col-form-label">Placa:</label>
                        <div class="col-sm-8">
                            <input name="placa" type="text" class="form-control" id="placa" placeholder="Ej: 1398XY">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="marca" class="col-sm-4 col-form-label">Marca:</label>
                        <div class="col-sm-8">
                            <input name="marca" type="text" class="form-control" id="marca" placeholder="Ej: Honda">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="modelo" class="col-sm-4 col-form-label">Modelo:</label>
                        <div class="col-sm-8">
                            <input name="modelo" type="text" class="form-control" id="modelo" placeholder="Ej: Pilot">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="color" class="col-sm-4 col-form-label">Color:</label>
                        <div class="col-sm-8">
                            <input name="color" type="text" class="form-control" id="color" placeholder="Ej: Azul">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="anio" class="col-sm-4 col-form-label">Año:</label>
                        <div class="col-sm-8">
                            <input name="anio" type="number" class="form-control" id="anio" placeholder="Ej: 2023">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="descripcion" class="col-sm-4 col-form-label">Tipo vehículo:</label>
                        <div class="col-sm-8">
                            <select name="id-tipo-vehiculo" id="id-tipo-vehiculo"></select>
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
            <button id="guardar-vehiculo" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Guardar
            </button>
        </div>
      </div>
    </div>
  </div>


  <!-- modal edit vehiculo-->
  <div class="modal fade" id="modal-edit-vehiculo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Editar vehiculo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="form-group row">
                        <label for="placa-edit" class="col-sm-4 col-form-label">Placa:</label>
                        <div class="col-sm-8">
                            <input name="placa-edit" type="text" class="form-control" id="placa-edit" placeholder="Ej: 1398XY">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="marca-edit" class="col-sm-4 col-form-label">Marca:</label>
                        <div class="col-sm-8">
                            <input name="marca-edit" type="text" class="form-control" id="marca-edit" placeholder="Ej: Honda">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="modelo-edit" class="col-sm-4 col-form-label">Modelo:</label>
                        <div class="col-sm-8">
                            <input name="modelo-edit" type="text" class="form-control" id="modelo-edit" placeholder="Ej: Pilot">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="color-edit" class="col-sm-4 col-form-label">Color:</label>
                        <div class="col-sm-8">
                            <input name="color-edit" type="text" class="form-control" id="color-edit" placeholder="Ej: Azul">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="anio-edit" class="col-sm-4 col-form-label">Año:</label>
                        <div class="col-sm-8">
                            <input name="anio-edit" type="number" class="form-control" id="anio-edit" placeholder="Ej: 2023">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="descripcion-edit" class="col-sm-4 col-form-label">Tipo vehículo:</label>
                        <div class="col-sm-8">
                            <select name="id-tipo-vehiculo-edit" id="id-tipo-vehiculo-edit"></select>
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
            <button id="actualizar-vehiculo" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Actualizar
            </button>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal repartidor --}}
  <div class="modal fade" id="modal-repartidor" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Asignar repartidor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-repartidor" data-show-export="false" data-search="true"
                        data-show-print="false" data-toggle="table"
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="nombre">Nombre</th>
                                <th data-sortable="true" data-field="paterno">Paterno</th>
                                <th data-sortable="true" data-field="telefono">Telefono</th>
                                <th data-sortable="true" data-field="licencia_conducir">Nro. Licencia</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        {{-- <div class="modal-footer">
            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                <i class="fas fa-door-open"></i>    
                Close
            </button>
            <button id="guardar-repartidor" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Asignar
            </button> --}}
        </div>
      </div>
    </div>
  </div>


  @stop

@section('css')

    {{-- stilos personalizados --}}
    <link rel="stylesheet" href="{{asset('/pizzeria/css/style.css')}}"/>

    {{-- bootstrap --}}
    <link rel="stylesheet" href="{{asset('/bootstrap/css/bootstrap-table.min.css')}}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    {{-- Alert --}}
    <link rel="stylesheet" href="{{asset('/bootstrap/css/alertify.min.css')}}"/>
    <!-- Default theme -->
    <link rel="stylesheet" href="{{asset('/bootstrap/css/default.min.css')}}"/>

    {{-- select 2 --}}
    <link rel="stylesheet" href="{{asset('/bootstrap/css/select2.min.css')}}"/>


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
    
    {{-- select 2 --}}
    <script src="{{asset('/bootstrap/js/select2.min.js')}}"></script>

    {{-- loader --}}
    <script src="{{asset('/bootstrap/js/spin.min.js')}}"></script>

    <script src="{{asset('/pizzeria/js/parametros.js')}}"></script>
    <script src="{{asset('/pizzeria/js/vehiculo.js')}}"></script>
@stop