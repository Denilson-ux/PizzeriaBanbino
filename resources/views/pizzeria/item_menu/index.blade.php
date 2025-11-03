@extends('adminlte::page')

@section('title', 'Items menú')

@section('content_header')
    <h1>Items menú</h1>
@stop

@section('content')

{{-- Loader --}}
<div id="loader-container"></div>

<!-- BAARRA DE NAVEGACION -->
<div class="card">
    <div class="card-header p-2">
        <ul class="nav nav-pills">
            <li class="nav-item"><a class="nav-link active" href="#item-menu-tab"
                    data-toggle="tab"><i class="fas fa-user"></i>&nbsp;&nbsp;Items menú</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="#item-menu-eliminados-tab"
                    data-toggle="tab"><i class="far fa-trash-alt"></i>&nbsp;&nbsp;Eliminados</a>
            </li>
        </ul>
    </div>
</div>


<div class="card-body">

    <div class="tab-content">
        <!------------------- INDEX ------------------->
        <div class="active tab-pane" id="item-menu-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div id="toolbar">
                        <a id="btn-nuevo-item-menu" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nuevo
                        </a>
                    </div>
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-item-menu" data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" data-toolbar="#toolbar"
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_item_menu">ID</th>
                                <th data-sortable="true" data-field="nombre">Nombre</th>
                                <th data-sortable="true" data-field="precio">Precio</th>
                                <th data-sortable="true" data-field="descripcion">Descripcion</th>
                                <th data-sortable="true" data-field="tipo_menu">Tipo item menú</th>
                                <th data-sortable="true" data-width="200" data-field="imagen_td">Imagen</th>
                                <th data-sortable="true" data-width="160" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!------------------- ELIMINADOS ------------------->
        <div class="tab-pane" id="item-menu-eliminados-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-item-menu-eliminados" 
                        data-show-export="true" data-search="true"
                        data-show-print="true" data-toggle="table" 
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_item_menu">ID</th>
                                <th data-sortable="true" data-field="nombre">Nombre</th>
                                <th data-sortable="true" data-field="precio">Precio</th>
                                <th data-sortable="true" data-field="descripcion">Descripcion</th>
                                <th data-sortable="true" data-field="tipo_menu">Tipo item menú</th>
                                <th data-sortable="true" data-width="200" data-field="imagen_td">Imagen</th>
                                <th data-sortable="true" data-width="160" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> 
        </div>
    </div>

</div>


<div class="modal fade" id="modal-nuevo-item-menu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Nuevo item menu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="form-group row">
                        <label for="nombre" class="col-sm-4 col-form-label">Nombre:</label>
                        <div class="col-sm-8">
                            <input name="nombre" type="text" class="form-control" id="nombre" placeholder="Ej: Pizza Pepperoni Clásico">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="precio" class="col-sm-4 col-form-label">Precio:</label>
                        <div class="col-sm-8">
                            <input name="precio" type="number" class="form-control" id="precio" placeholder="Ej: 10" step="any">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="descripcion" class="col-sm-4 col-form-label">Descripción:</label>
                        <div class="col-sm-8">
                            <textarea name="descripcion" class="form-control" id="descripcion" placeholder="Ej: Nuestra clásica pizza de pepperoni con una base de salsa de tomate casera, mozzarella fundida y rodajas generosas de pepperoni premium."></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="descripcion-edit" class="col-sm-4 col-form-label">Tipo item menú:</label>
                        <div class="col-sm-8">
                            <select name="id-tipo-menu" id="id-tipo-menu"></select>
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
            <button id="guardar-item-menu" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Guardar
            </button>
        </div>
      </div>
    </div>
  </div>


  <!-- modal edit item-menu-->

  <div class="modal fade" id="modal-edit-item-menu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Editar item-menu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <div class="form-group row">
                        <label for="nombre-edit" class="col-sm-4 col-form-label">Nombre:</label>
                        <div class="col-sm-8">
                            <input name="nombre-edit" type="text" class="form-control" id="nombre-edit" placeholder="Ej: Pollo al jugo">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="precio-edit" class="col-sm-4 col-form-label">Precio:</label>
                        <div class="col-sm-8">
                            <input name="precio-edit" type="number" class="form-control" id="precio-edit" placeholder="Ej: 10" step="any">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="descripcion-edit" class="col-sm-4 col-form-label">Descripción:</label>
                        <div class="col-sm-8">
                            <textarea name="descripcion-edit" class="form-control" id="descripcion-edit" placeholder="Ej: Especial con extra de carne"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="id-tipo-menu-edit" class="col-sm-4 col-form-label">Tipo item menú:</label>
                        <div class="col-sm-8">
                            <select name="id-tipo-menu-edit" id="id-tipo-menu-edit"></select>
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
            <button id="actualizar-item-menu" type="button" class="btn btn-success">
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
    <script src="{{asset('/pizzeria/js/item_menu.js')}}"></script>

    <script>
      // Aumentar columna Acciones y agregar botón Gestionar receta desde el formatter en item_menu.js si existe.
      // Si no, agrega aquí un formatter básico para la columna 'acciones'.
      if (typeof window.accionesFormatter === 'undefined') {
        window.accionesFormatter = function(value, row) {
          const gestionar = `<a href="/admin/item-menu/${row.id_item_menu}/receta" class="btn btn-sm btn-secondary mr-1">Gestionar receta</a>`;
          const editar = `<a class="btn btn-sm btn-warning mr-1" onclick="window.editarItem && window.editarItem(${row.id_item_menu})">Editar</a>`;
          const eliminar = `<a class="btn btn-sm btn-danger" onclick="window.eliminarItem && window.eliminarItem(${row.id_item_menu})">Eliminar</a>`;
          return gestionar + editar + eliminar;
        }
        // Enlazar formatter si la tabla aún no lo tiene
        document.addEventListener('DOMContentLoaded', function() {
          const $tabla = $('#tabla-item-menu');
          if ($tabla.length) {
            const cols = $tabla.bootstrapTable('getOptions').columns || [];
            // Si no hay definidas columnas programáticamente, no tocamos.
          }
        });
      }
    </script>
@stop