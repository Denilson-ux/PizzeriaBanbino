@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Detalle del pedido</h1>
@stop

@section('content')

<div class="container-fluid" style="">
    <div class="">
        <div class="row row_dash">
            <div class="col-lg-12">
                <div class="col-4 m-3">
                    <a href="{{asset('admin/pedido')}}" class="btn btn-info">
                        <i class="fas fa-arrow-left"></i> Regresar 
                    </a>
                </div>
                <div class="card">
                    <div class=" card-signin">
                        <div class="card-body">
                            <h5 class=""><i class="fa fa-shopping-cart"></i> Detalle del pedido</h5>
                            <hr>
                            <div class="alert alert-success" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <p>Aquí puedes ver el detalle del pedido.</p>
                            </div>
                            <div class="row">
                                <!-- 1ra columna -->
                                <div class="col-6">
                                    <div class="col-12">
                                        <h5>Información del cliente</h5>
                                        <hr>
                                    </div>

                                    <div class="col-lg-12 ">
                                        <div class="form-group row">
                                            <div class="col-3">
                                                <label for="nombre" class="">Nombres:</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <label id="nombre"></label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-3">
                                                <label for="correo" class="">Correo:</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <p id="correo"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group row">
                                            <div class="col-3">
                                                <label for="telefono" class="">Telefono:</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <p id="telefono"></p>
                                            </div>
                                        </div>
                                        {{-- <div class="form-group row">
                                            <div class="col-3">
                                                <label for="ci" class="">CI:</label>
                                            </div>
                                            <div class="col-sm-9">
                                                <p id="ci"></p>
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="col-12">
                                        <h5>Datos del pedido</h5>
                                        <hr>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row">
                                            <div class="col-5">
                                                <label for="id_pedido" class="">Pedido:</label>
                                            </div>

                                            <div class="col-sm-7">
                                                <p id="id_pedido"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group row">
                                            <div class="col-5">
                                                <label for="fecha" class="">Fecha:</label>

                                            </div>
                                            <div class="col-sm-7">
                                                <p id="fecha"></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-5">
                                                <label for="estado_pedido" class="">Estado de entrega del pedido:</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <p id="estado_pedido"></p>
                                            </div>
                                        </div>
                                        <div class="form-group row d-none" id="pedido-paypal">
                                            <div class="col-5">
                                                <label for="nro_transaccion" class="">Nro transacción:</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <p id="nro_transaccion"></p>
                                            </div>
                                            <div class="col-5">
                                                <label for="descripcion_pago" class="">Estado pago paypal:</label>
                                            </div>
                                            <div class="col-sm-7">
                                                <p id="descripcion_pago"></p>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-5">
                                                <label for="monto" class="">Total Bs.:</label>

                                            </div>
                                            <div class="col-sm-7">
                                                <p id="monto"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="col-12">
                                        <h5>Dirección</h5>
                                        <hr>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-3">
                                                <label for="direccion" class="">Dirección:</label>

                                            </div>
                                            <div class="col-sm-8">
                                                <p id="direccion"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div id="map">

                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="col-12">
                                        <h5>Detalle del pedido</h5>
                                        <hr>
                                    </div>
                                    <div class="col-12">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Nombre</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tabla-detalle-pedido">
                                                
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

    <style>
        #map {
            width: 100%;
            height: 300px;
        }

    </style>

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

    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAtjzxI9iVgpXTkQ6kFJ33RA5oGngdS6d0&libraries=places,directions,geometry"
      type="text/javascript">
    </script>

    <script>
        const idPedido = <?=$idPedido?>
    </script>
    <script src="{{asset('/pizzeria/js/cliente_web/detalle_pedido.js')}}"></script>
@stop