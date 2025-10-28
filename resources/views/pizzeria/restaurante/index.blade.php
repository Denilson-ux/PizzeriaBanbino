@extends('adminlte::page')

@section('title', 'Restaurante')

@section('content_header')
    <h1>Restaurante</h1>
@stop

@section('content')

<div class="container mt-5">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" required>
            </div>
            

            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" required>
            </div>

            <div id="map-container">
                <div id="map"></div>
                <button id="ubicacion-actual-btn" type="button" title="Ubicación actual">
                    <div class="d-flex justify-content-center align-items-center">
                        <i class="fa fa-map-marker"></i>
                    </div>
                </button>
                <input type="text" name="" id="latitud" class="d-none">
                <input type="text" name="" id="longitud" class="d-none">
            </div>
        </div>

        <div class="col-md-6">
            {{-- <div class="mb-3">
                <label for="horario_apertura" class="form-label">Horario de Apertura</label>
                <input type="time" class="form-control" id="horario_apertura" name="horario_apertura" required>
            </div>

            <div class="mb-3">
                <label for="horario_cierre" class="form-label">Horario de Cierre</label>
                <input type="time" class="form-control" id="horario_cierre" name="horario_cierre" required>
            </div> --}}
            

            <div class="mb-3">
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>

            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
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

    <button type="button" id="guardar-restaurante" class="btn btn-primary my-3">Guardar</button>

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

    {{-- Google maps --}}
    <script async defer
      src="{{asset('/bootstrap/js/googlemaps.js')}}"
      type="text/javascript"></script>


    <script src="{{asset('/pizzeria/js/parametros.js')}}"></script>
    <script src="{{asset('/pizzeria/js/restaurante.js')}}"></script>
@stop
