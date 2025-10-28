

let pedido = [];
let table = $("#tabla-pedido");

$(document).ready(() => {
    cargarPedido();
    cargarInformacionCliente();
});

function cargarPedido() {
    showLoader();
    const url = rutaApiRest + "pedido/" + idPedido; //idPedido viene de la vista detalle_pedido
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            pedido = response.data;
            cargarDetallePedidio(pedido);

            hideLoader();
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);

            hideLoader();
        }

    });
}

function cargarDetallePedidio(pedido) {
    $("#nombre").text(pedido.cliente.persona.nombre);
    $("#correo").text(pedido.cliente.persona.correo);
    $("#telefono").text(pedido.cliente.persona.telefono);
    $("#direccion").text(pedido.ubicacion.referencia);

    $("#id_pedido").text(pedido.id_pedido);
    $("#fecha").text(pedido.fecha);
    $("#estado_pedido").text(pedido.estado_pedido);
    $("#monto").text(pedido.monto);

    if (pedido.nro_transaccion != null) {
        $("#pedido-paypal").removeClass("d-none");
        $("#nro_transaccion").text(pedido.nro_transaccion);
        $("#descripcion_pago").text(pedido.descripcion_pago);
    }

    initMap(pedido);
    cargarTablaDetalle(pedido.detalle_pedido);
}

function cargarTablaDetalle(detallePedido) {
    const tabla = $("#tabla-detalle-pedido");
    detallePedido.forEach(element => {
        const cuerpo = `
            <tr>
                <!-- <td>${element.id_item_menu}</td> -->
                <td>${element.item_menu.nombre}</td>
                <td>${element.cantidad}</td>
                <td>${element.item_menu.precio}</td>
            </tr>
        `;
        tabla.append(cuerpo);
    });
}



function cargarInformacionCliente() {
    const clientemall = JSON.parse(localStorage.getItem('clientemall'));
    if (clientemall) {
        $("#nombre-usuario").text(clientemall.user.name);
    }
}

function initMap(pedido) {
    try {
        const latitud = parseFloat(pedido.ubicacion.latitud);
        const longitud = parseFloat(pedido.ubicacion.longitud);
        var myLatLng = { lat: latitud, lng: longitud };
        var mapOptions = {
            center: myLatLng,
            zoom: 8,
            streetViewControl: false,
        };
        var map = new google.maps.Map(document.getElementById('map'), mapOptions);
        var marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            draggable: false,
            title: 'Mi marcador',
        });
    } catch (error) {
        console.log("Error en la carga del mapa", error);
    }
}
