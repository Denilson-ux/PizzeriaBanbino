@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Clientes</h1>
@stop

@section('content')

{{-- Loader --}}
<div id="loader-container"></div>

<!-- BAARRA DE NAVEGACION -->
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#cliente-tab"
                    data-toggle="tab"><i class="fas fa-user"></i>&nbsp;&nbsp;Clientes</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#cliente-eliminados-tab"
                    data-toggle="tab"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Eliminados</a>
            </li>
        </ul>
    </div>
</div>


<div class="card-body">

    <div class="tab-content">
        <!------------------- INDEX ------------------->
        <div class="active tab-pane" id="cliente-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div id="toolbar">
                        <a id="btn-nuevo-cliente" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo
                        </a>
                    </div>
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-cliente" data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" data-toolbar="#toolbar"
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_cliente">ID</th>
                                <th data-sortable="true" data-field="nombre">Nombre</th>
                                <th data-sortable="true" data-field="paterno">Paterno</th>
                                <th data-sortable="true" data-field="materno">Materno</th>
                                <th data-sortable="true" data-field="telefono">Telefono</th>
                                <th data-sortable="true" data-field="correo">Correo</th>
                                <th data-sortable="true" data-field="descuento">Descuento %</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!------------------- ELIMINADOS ------------------->
        <div class="tab-pane" id="cliente-eliminados-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-cliente-eliminados" 
                        data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" 
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_cliente">ID</th>
                                <th data-sortable="true" data-field="nombre">Nombre</th>
                                <th data-sortable="true" data-field="paterno">Paterno</th>
                                <th data-sortable="true" data-field="materno">Materno</th>
                                <th data-sortable="true" data-field="telefono">Telefono</th>
                                <th data-sortable="true" data-field="correo">Correo</th>
                                <th data-sortable="true" data-field="descuento">Descuento %</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> 
        </div>
    </div>

</div>


<!-- modal nuevo cliente-->
<div class="modal fade" id="modal-nuevo-cliente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Nuevo Cliente</h5>
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
                        <label for="descuento" class="col-sm-4 col-form-label">Descuento %:</label>
                        <div class="col-sm-8">
                            <input name="descuento" type="number" class="form-control" id="descuento" placeholder="Ej: 10" value="0">
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
            <button id="guardar-cliente" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Guardar
            </button>
        </div>
      </div>
    </div>
  </div>


  <!-- modal edit cliente-->

  <div class="modal fade" id="modal-edit-cliente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Editar Cliente</h5>
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
                        <label for="descuento-edit" class="col-sm-4 col-form-label">Descuento %:</label>
                        <div class="col-sm-8">
                            <input name="descuento-edit" type="number" class="form-control" id="descuento-edit" placeholder="Ej: 10" value="0">
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
            <button id="actualizar-cliente" type="button" class="btn btn-success">
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
    <script src="{{asset('/pizzeria/js/cliente.js')}}"></script>
@stop