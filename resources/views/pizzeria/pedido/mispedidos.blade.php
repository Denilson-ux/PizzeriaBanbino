@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Mis pedidos - {{$user['persona']['nombre'] . " " . $user['persona']['paterno']}}</h1>
@stop

@section('content')

{{-- Loader --}}
<div id="loader-container"></div>


<div class="card-body">

    <div class="tab-content">
        <!------------------- INDEX ------------------->
        <div class="active tab-pane" id="pedido-tab">
            <div class="row">
                <div class="col-lg-12 col-sm-12">
                    <table id="tabla-pedido"
                            data-toggle="table"
                            data-show-print="true"
                            data-show-columns="true"
                            data-search="true"
                            data-show-export="true"
                            data-show-export="true"
                            data-pagination="true"
                            data-toolbar="#toolbar"
                            data-detail-view="true"
                            data-detail-formatter="detailFormatter"
                            data-row-style="rowStyle">

                        <thead>
                            <tr>
                                <th data-sortable="true" data-field="id_pedido">ID</th>
                                <th data-sortable="true" data-field="cliente">Cliente</th>
                                <th data-sortable="true" data-field="telefono">Telefono</th>
                                <th data-sortable="true" data-field="estado_pedido">Estado pedido</th>
                                <th data-sortable="true" data-field="repartidor">Repartidor</th>
                                <th data-sortable="true" data-field="metodo_pago">Método de pago</th>
                                <th data-sortable="true" data-field="monto">Total</th>
                                <th data-sortable="true" data-width="100" data-field="acciones">Acción</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- modal repartidores-->
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
                    <div class="form-group d-flex align-items-center" >
                        <label for="id-repartidor" class="col-sm-4 col-form-label">Repartidores:</label>
                        <div class="col-6">
                            <select  name="id-repartidor" id="id-repartidor" class="form-control">
                            </select>
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
            <button id="asignar-repartidor" type="button" class="btn btn-success">
                <i class="far fa-save"></i> Asignar
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

    {{-- select 2 --}}
    <link rel="stylesheet" href="{{asset('/bootstrap/css/select2.min.css')}}"/>

    {{-- Alert --}}
    <link rel="stylesheet" href="{{asset('/bootstrap/css/alertify.min.css')}}"/>
    <!-- Default theme -->
    <link rel="stylesheet" href="{{asset('/bootstrap/css/default.min.css')}}"/>



@stop

@section('js')

    <script>
        const user = <?=$user?>
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

    {{-- select 2 --}}
    <script src="{{asset('/bootstrap/js/select2.min.js')}}"></script>s

    {{-- perzonalizado --}}
    {{-- loader --}}
    <script src="{{asset('/bootstrap/js/spin.min.js')}}"></script>


    <script src="{{asset('/pizzeria/js/parametros.js')}}"></script>
    <script src="{{asset('/pizzeria/js/pedido/mispedidos.js')}}"></script>
@stop