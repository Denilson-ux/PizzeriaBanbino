@extends('adminlte::page')

@section('title', 'Crear Menú')

@section('content_header')
    <h1>Nueva venta</h1>
@stop

@section('content')

<div class="card">
    <div class="row">
        <div class="col-4 m-3">
            <a href="{{asset('admin/nota-venta')}}" class="btn btn-infor">
                <i class="fas fa-arrow-left"></i> Regresar 
            </a>
        </div>
    </div>
    <div class="card-header">
        Datos
    </div>
    <div class="card-body">
        <div class="row d-flex">
            {{-- <div class="col-lg-6 col-md-6 col-xl-6 col-sm-12">
                <div class="form-group d-flex" >
                    <label for="id-menu" class="col-sm-3 col-form-label">Menús:</label>
                    <div class="col-9">
                        <select  name="id-menu" id="id-menu" class="form-control">
                            <option value="0">Seleccione el menú</option>
                        </select>
                    </div>
                </div>
            </div> --}}
            <div class="col-lg-6 col-md-6 col-xl-6 col-sm-12">
                <div class="form-group d-flex align-items-center" >
                    <label for="id-cliente" class="col-sm-4 col-form-label">Cliente:</label>
                    <div class="col-6">
                        <select  name="id-cliente" id="id-cliente" class="form-control">
                        </select>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-sm btn-info" id="btn-search-cliente">
                            <i class="fa fa-search"></i>
                        </button>
                        <button class="btn btn-sm btn-success" id="btn-nuevo-cliente">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="" hidden>
                        <label for="fecha" class="col-sm-2 col-form-label">Fecha:</label>
                        <div class="col-3">
                            <input type="date" class="form-control" id="fecha" name="fecha" disabled>
                        </div>
                    </div>
                    <label for="descuento-venta" class="col-sm-4 col-form-label">Descuento %:</label>
                    <div class="col-3">
                        <input type="text" class="form-control" id="descuento-venta" name="descuento-venta" disabled value="0.00">
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-xl-6 col-sm-12">
                
                <div class="form-group d-flex" >
                    <label for="monto" class="col-sm-4 col-form-label">Monto total:</label>
                    <div class="col-6">
                        <input type="text" class="form-control" id="monto" name="monto" disabled>
                    </div>
                    
                </div>
                <div class="form-group d-flex" >
                    <label for="monto-descuento" class="col-sm-4 col-form-label">Monto con desc.:</label>
                    <div class="col-6">
                        <input type="text" class="form-control" id="monto-descuento" name="monto-descuento" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Primera mitad para mostrar los primeros 3 items disponibles -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title my-2">Items Disponibles</h5>
                <div class="form-group">
                    <input type="text" class="form-control" id="buscar" placeholder="Escribe para buscar...">
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="content-card-disponible">
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda mitad para mostrar los siguientes 3 items disponibles -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title my-2">Items agregados</h5>
                <div class="form-group">
                    <input type="text" class="form-control" id="buscar-add" placeholder="Escribe para buscar...">
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="content-card-add">
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Modal nuevo cliente --}}
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

  {{-- modal lista de clientes --}}
  <div class="modal fade" id="modal-lista-cliente" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticBackdropLabel">Lista de clientes</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <table 
                        class="table-bordered table-hover table-striped"
                        id="tabla-cliente-venta" 
                        data-show-export="false" data-search="true"
                        data-show-print="false" data-toggle="table" 
                        data-height="100%" data-only-info-pagination="false"
                        data-pagination="true" data-show-columns="true">
                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="nombre">Nombre</th>
                                <th data-sortable="true" data-field="paterno">Paterno</th>
                                <th data-sortable="true" data-field="telefono">Telefono</th>
                                <th data-sortable="true" data-field="descuento">Descuento %</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> 
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
                <i class="fas fa-door-open"></i>    
                Close
            </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Botón flotante para agregar al carrito -->

<div class="floating-btn">
    <button class="btn btn-primary" id="guardar-nota-venta" data-toggle="modal" data-target="#carritoModal">
        <i class="fa fa-save"></i> Guardar
    </button>
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

    {{-- select 2 --}}
    <link rel="stylesheet" href="{{asset('/bootstrap/css/select2.min.css')}}"/>


@stop

@section('js')

    <script>
        const user = <?=$user?>;
    </script>

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

    {{-- perzonalizado --}}
    {{-- loader --}}
    <script src="{{asset('/bootstrap/js/spin.min.js')}}"></script>


    {{-- select 2 --}}
    <script src="{{asset('/bootstrap/js/select2.min.js')}}"></script>

    <script src="{{asset('/pizzeria/js/parametros.js')}}"></script>
    <script src="{{asset('/pizzeria/js/cliente.js')}}"></script>
    <script src="{{asset('/pizzeria/js/nota_venta/create_nota_venta.js')}}"></script>
@stop