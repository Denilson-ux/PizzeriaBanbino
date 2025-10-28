@extends('cliente_web.layoutweb')


@section('contentweb')

<div class="container-fluid" style="margin-top: 100px">
    <div class="">
        <div class="row row_dash">
            <div class="col-lg-12">
                <div class="card">
                    <div class=" card-signin">
                        <div class="card-body">
                            <h5 class=""><i class="fas fa-list"></i> Mis pedidos</h5>
                            <hr>
                            <div class="alert alert-success" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <p>Aquí puedes todos tus pedidos.</p>
                            </div>
                            <div class="row" id="contenedor-pedidos">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@section('clienteweb-css')
@endsection

  @section('clienteweb-js')

    <script>
        const idCliente = <?=$idCliente?>
    </script>
    <script src="{{asset('/pizzeria/js/cliente_web/mis_pedidos.js')}}"></script>

  @endsection