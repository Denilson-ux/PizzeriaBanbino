<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota de Venta</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #fff; }
        .contenedor { max-width: 250px; margin: 10px auto; padding: 5px; border: 1px solid #ddd; box-shadow: 0 0 3px rgba(0, 0, 0, 0.1); }
        .centrar-tex { text-align: center; margin-bottom: 5px; }
        .imagenLogo { max-width: 100%; height: auto; margin-bottom: 5px; }
        .salto-linea { margin-top: 5px; }
        .negrita { font-weight: bold; font-size: 8px; }
        .texto-mayuscula { text-transform: uppercase; }
        .datos-venta { font-size: 10px; }
        hr { border: none; border-top: 1px solid #ddd; margin: 3px 0; }
        .text-center { text-align: center; }
        #btnImprimir { padding: 3px; background-color: #4caf50; color: #fff; text-align: center; text-decoration: none; font-size: 10px; cursor: pointer; border-radius: 3px; display: block; margin: 5px auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        #pdf2 { padding: 10px; border: 1px solid #ddd; box-shadow: 0 0 3px rgba(0, 0, 0, 0.1); }
        #pdf2 .salto-linea, #pdf2 .centrar-tex { margin-top: 10px; }
        #pdf2 .salto-linea:last-child { margin-bottom: 10px; }
        #pdf2 p.text-center { margin-top: 15px; }
    </style>
</head>

<body>

    <div class="contenedor">
        <div id="pdf2">
            <div class="centrar-tex"></div>
            <h6 class="centrar-tex">Nota de Venta</h6>
            <h6 id="" class="centrar-tex texto-mayuscula">Mi pizzeria</h6>
            <p id="direccion-restaurante" class="texto-mayuscula datos-venta centrar-tex">Montero</p>
            <div class="salto-linea">
                <strong class="datos-venta">Teléfono:</strong>
                <span id="telefono-restaurante" class="datos-venta">70054805</span><br>
                <strong class="datos-venta">Fecha: </strong>
                <span id="fecha" class="datos-venta">{{ optional($venta['created_at'])->timezone(config('app.timezone'))->format('Y-m-d H:i:s') ?? now()->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</span><br>
            </div>
            <div class="centrar-tex">
                <strong class="datos-venta">Cliente</strong>
            </div>
            <div class="salto-linea">
                <strong class="datos-venta">Nombre: </strong>
                <span id="nombre-cliente" class="datos-venta">{{$venta['persona_cliente']['nombre']." ".$venta['persona_cliente']['paterno']}}</span><br>
                <strong class="datos-venta">CI: </strong>
                <span id="ci-cliente" class="datos-venta">{{$venta['persona_cliente']['ci']}}</span><br>
                <strong class="datos-venta">Teléfono: </strong>
                <span id="telefono-cliente" class="datos-venta">{{$venta['persona_cliente']['telefono']}}</span>
            </div>

            <div>
                <div class="centrar-tex">
                    <strong class="datos-venta">Detalle</strong>
                </div>
                <hr>
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="datos-venta text-left">Descripción</th>
                            <th class="datos-venta text-left">Precio</th>
                            <th class="datos-venta text-left">Cant.</th>
                            <th class="datos-venta text-left">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-detalle-venta">
                        @php $montoSinDescuento = 0; @endphp
                        @foreach ($venta['detalle_venta'] as $detalle)
                            <tr>
                                <td class="datos-venta text-left"><span>{{$detalle['item_menu']['nombre']}}</span></td>
                                <td class="datos-venta text-left"><span>{{$detalle['item_menu']['precio']}}</span></td>
                                <td class="datos-venta text-left"><span>{{$detalle['cantidad']}} X</span></td>
                                <td class="datos-venta text-left"><span>{{$detalle['sub_monto']}}</span></td>
                                @php $montoSinDescuento += (float)$detalle['sub_monto']; @endphp
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="salto-linea">
                <div style="display: flex; justify-content: space-between;">
                    <div></div>
                    <div style="text-align: right;">
                        <strong class="negrita datos-venta">Total:</strong>
                        <span class="datos-venta" id="total">{{$montoSinDescuento}}</span><br>
                        <strong id="descuento-porcentaje" class="negrita datos-venta">Descuento %</strong>
                        <span class="datos-venta" id="descuento"> {{$venta['cliente']['descuento']}} - Bs. {{((float)($venta['cliente']['descuento'])/100)*$montoSinDescuento}}</span><br>
                        <strong class="negrita datos-venta">Total a pagar</strong>
                        <span class="datos-venta" id="total-pagar">Bs. {{$venta['monto']}}</span>
                    </div>
                </div>
                <hr>
                <strong class="negrita datos-venta">Cajero:</strong>
                <span class="datos-venta" id="nombre-usuario">{{$venta['persona_empleado']['nombre']." ".$venta['persona_empleado']['paterno']}}</span><br>
                <strong class="negrita datos-venta">Comprobante:</strong>
                <span class="datos-venta" id="id-venta">{{$venta['id_nota_venta']}}</span>
            </div>
            <hr>
            <p class="text-center">Hecho por www.pizzeria.store</p>
        </div>
    </div>

</body>

</html>
