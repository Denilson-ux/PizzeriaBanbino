@extends('adminlte::page')

@section('title', 'Crear Menú')

@section('content_header')
    <h1>Catálogo Menú</h1>
@stop

@section('content')

<div class="card">
    <div class="row">
        <div class="col-4 m-3">
            <a href="{{asset('admin/menu')}}" class="btn btn-infor">
                <i class="fas fa-arrow-left"></i> Regresar 
            </a>
        </div>
    </div>
    <div class="card-header">
        Datos
    </div>
    <div class="card-body">
        <div class="row d-flex">
            <div class="col-lg-5 col-md-5 col-xl-5 col-sm-12">
                <div class="form-group d-flex" >
                    <label for="nombre" class="col-sm-3 col-form-label">Nombre:</label>
                    <div class="col-9">
                        <input name="nombre" id="nombre" type="text" class="form-control" placeholder="nombre">
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-xl-7 col-sm-12">
                <div class="form-group d-flex" >
                    <label for="descripcion" class="col-sm-3 col-form-label">Descripción:</label>
                    <div class="col-9">
                        <textarea name="descripcion" id="descripcion" type="text" class="form-control" placeholder="Descripción"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-xl-5 col-sm-12">
                <div class="form-group d-flex" >
                    <label for="fecha" class="col-sm-3 col-form-label">Fecha:</label>
                    <div class="col-9">
                        <input name="fecha" id="fecha" type="date" class="form-control" required>
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

<!-- Botón flotante para agregar al carrito -->

<div class="floating-btn">
    <button class="btn btn-primary" id="guardar-menu" data-toggle="modal" data-target="#carritoModal">
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

    {{-- perzonalizado --}}
    {{-- loader --}}
    <script src="{{asset('/bootstrap/js/spin.min.js')}}"></script>


    <script src="{{asset('/pizzeria/js/parametros.js')}}"></script>
    <script src="{{asset('/pizzeria/js/menu/create_menu.js')}}"></script>

    <script>
    </script>
@stop