
$(document).ready(() => {
    cargarPedidosCliente();
    cargarInformacionCliente();
})

function cargarPedidosCliente() {
    const url = rutaApiRest + "pedido-cliente/" + idCliente; //idCliente viene de la vista mis_pedidos
    showLoader();
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            const pedidos = response.data.pedido;
            console.log(response);
            cargarCardPedidos(pedidos);
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

function cargarCardPedidos(pedidos) {
    const contenedor = $("#contenedor-pedidos");
    pedidos.forEach(element => {
        const fechaHora = obtenerFechaHoraFormateada(element.created_at);
        const cuerpo = `
                <div class="card mt-3 col-lg-3 col-md-3 col-sm-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title">Pedido: ${element.id_pedido}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Fecha y hora: <strong>${fechaHora.fecha + " - " + fechaHora.hora}</strong></p>
                    <p class="card-text">Total: <strong>$ ${element.monto}</strong></p>
                    <a type="button" class="btn btn-warning" href="${rutaLocal + 'detalle/' + element.id_pedido}">
                        <i class="fas fa-search"></i>
                        Ver Detalles
                    </a>
                </div>
            </div>
        `;
        contenedor.append(cuerpo);
    });
}

function cargarInformacionCliente() {
    const clientemall = JSON.parse(localStorage.getItem('clientemall'));
    if (clientemall) {
        $("#nombre-usuario").text(clientemall.user.name);
    }
}

function obtenerFechaHoraFormateada(fechaHoraString) {
    const fechaHora = new Date(fechaHoraString);

    // Obtener la fecha en formato legible (yyyy-mm-dd)
    const fecha = fechaHora.toISOString().split('T')[0];

    // Obtener la hora en formato legible (hh:mm:ss)
    const hora = fechaHora.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

    return { fecha, hora };
}