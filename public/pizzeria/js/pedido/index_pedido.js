let pedidos = [];
let repartidores = [];
let tablePendiente = $("#tabla-pedido-pendiente");
let tableConfirmado = $("#tabla-pedido-confirmado");
let tableEntregado = $("#tabla-pedido-entregado");
let tableRechazado = $("#tabla-pedido-rechazado");

$(document).ready(() => {
    cargarPedido();
    cargarClientesAsync();
});

$(document).on("click", ".confirmar", function () {
    const idPedido = $(this).attr("data-id");
    const pedido = pedidos.find(element => element.id_pedido == idPedido);

    alertify.confirm("Confirmar", "¿Está seguro de confirmar el pedido?",
        function () {
            updatePedido(idPedido, "Confirmado");
        },
        function () {
            alertify.error('Rechazado');
        });

});

$(document).on("click", ".repartidor", function () {
    const idPedido = $(this).attr("data-id");
    const pedido = pedidos.find(element => element.id_pedido == idPedido);

    $("#asignar-repartidor").attr("name", idPedido);
    $("#modal-repartidor").modal('show');
    cargarSelectRepartidor(repartidores, pedido.id_repartidor);
});


$(document).on("click", "#asignar-repartidor", function () {
    const idPedido = this.name;
    const idRepartidor = $("#id-repartidor").val();
    updatePedido(idPedido, "Confirmado", idRepartidor);
    $("#modal-repartidor").modal('hide');
});

$(document).on("click", ".rechazar", function () {
    const idPedido = $(this).attr("data-id");
    const pedido = pedidos.find(element => element.id_pedido == idPedido);

    $("#rachazar-pedido").attr("name", idPedido);
    $("#modal-rechazar").modal('show');
});

$(document).on("click", "#rachazar-pedido", function () {
   
    const idPedido = this.name;
    const descripcion = $("#descripcion").val();
    alertify.confirm("Rechazar", "¿Está seguro de rechazar el pedido?",
        function () {
            updatePedido(idPedido, "Rechazado", null, descripcion);
            $("#modal-rechazar").modal('hide');
        },
        function () {
            alertify.error('Rechazado');
        }
    );
});


$(document).on("click", ".detalle", function () {
    const idPedido = $(this).attr("data-id");
    window.location.href = "pedido/detalle/" + idPedido;
    // const enlaceTemporal = document.createElement('a');
    // enlaceTemporal.href = rutaLocal + "admin/pedido/detalle/" + idPedido;
    // enlaceTemporal.click();
});


function cargarPedido() {
    // showLoader();
    const url = rutaApiRest + "pedido";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            pedidos = response.data;

            const pedidosPendientes = construirObjeto(pedidos, "Pendiente");
            const pedidosConfirmados = construirObjeto(pedidos, "Confirmado");
            const pedidoEntregados = construirObjeto(pedidos, "Entregado");
            const pedidoRechazados = construirObjeto(pedidos, "Rechazado");

            tablePendiente.bootstrapTable('load', pedidosPendientes);
            tableConfirmado.bootstrapTable('load', pedidosConfirmados);
            tableEntregado.bootstrapTable('load', pedidoEntregados);
            tableRechazado.bootstrapTable('load', pedidoRechazados);

            console.log(pedidos);
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

function construirObjeto(pedidos, estadoPedido) {
    let acciones = {};
    const pedidosObject = pedidos.filter(element => element.estado_pedido == estadoPedido);
    const objectTable = [];

    switch (estadoPedido) {
        case "Pendiente":
            acciones = {
                confirmar: "",
                repartidor: "d-none",
                rechazar: "",
                detalle: "",
            }
            break;
        case "Confirmado":
            acciones = {
                confirmar: "d-none",
                repartidor: "",
                rechazar: "",
                detalle: "",
            }
            break;
        case "Entregado":
            acciones = {
                confirmar: "d-none",
                repartidor: "d-none",
                rechazar: "d-none",
                detalle: "",
            }
            break;
        case "Rechazado":
            acciones = {
                confirmar: "",
                repartidor: "d-none",
                rechazar: "d-none",
                detalle: "",
            }
            break;
        default:
            break;
    }

    pedidosObject.forEach(element => {

        const object = {};
        object.id_pedido = element.id_pedido;
        object.cliente = element.cliente.persona.nombre + " " + element.cliente.persona.paterno;
        object.telefono = element.cliente.persona.telefono;
        object.estado_pedido = element.estado_pedido;
        object.descripcion = element.descripcion;
        object.repartidor = element.id_repartidor == null ? null : element.repartidor.persona.nombre + " " +
            element.repartidor.persona.paterno;
        object.metodo_pago = element.tipo_pago.nombre;
        object.monto = element.monto;
        object.detalle_pedido = element.detalle_pedido;

        object.acciones = `
                        <a data-id="${object.id_pedido}" class="btn btn-primary btn-sm confirmar ${acciones.confirmar}" title="Confirmar"><i class="fa fa-check"></i></a>
                        <a data-id="${object.id_pedido}" class="btn btn-success btn-sm repartidor ${acciones.repartidor}" title="Asignar repartidor"><i class="fas fa-motorcycle"></i></a>
                        <a data-id="${object.id_pedido}" class="btn btn-danger btn-sm rechazar ${acciones.rechazar}" title="Rechazar"><i class="fa fa-times"></i></a>
                        <a data-id="${object.id_pedido}" class="btn btn-secondary btn-sm detalle ${acciones.detalle}" title="Detalles"><i class="fa fa-eye"></i></a>
                        `;

        objectTable.push(object);
    });
    return objectTable;
}

function updatePedido(idPedido, estadoPedido, idRepartidor = null, descripcion = null) {
    const data = {};
    data.id_repartidor = idRepartidor;
    data.estado_pedido = estadoPedido;
    data.descripcion = descripcion;
    datosEnviar = JSON.stringify(data);
    const url = rutaApiRest + "pedido/" + idPedido;
    console.log(datosEnviar);

    showLoader();
    $.ajax({
        url: url,
        type: "POST",
        data: datosEnviar,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {

                const alerta = alertify.alert("Confirmado", "¡Súper, todo salió correctamente!");
                setTimeout(function () {
                    alerta.close();;
                }, 1000);

                cargarPedido();
            } else {
                alertify.alert(
                    "Error",
                    "Error, ocurrio un problema!"
                );
            }

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


function cargarRepartidorPromise() {
    return new Promise((resolve, reject) => {
        const url = rutaApiRest + "repartidor";
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (response) {
                console.log(response);
                const repartidores = response.data;
                resolve(repartidores);
            },
            error: function (data, textStatus, jqXHR, error) {
                console.log(data);
                console.log(textStatus);
                console.log(jqXHR);
                console.log(error);
                reject(error); // Rechaza la Promise en caso de error
            }
        });
    });
}

async function cargarClientesAsync() {
    try {
        repartidores = await cargarRepartidorPromise();
        cargarSelectRepartidor(repartidores);
    } catch (error) {
    }
}

function cargarSelectRepartidor(array, id = 0) {
    const select = $("#id-repartidor");
    select.empty();
    array.forEach(element => {
        let selected = "";
        if (id == element.id_repartidor) {
            selected = "selected";
        }
        select.append(
            `<option value="${element.id_repartidor}" ${selected}>${element.persona.nombre + " " + element.persona.paterno}</option>`
        );
    });
    select.select2({
        width: '100%',
        theme: "classic",
        // maximumSelectionLength: 1
    });
}


//detalle venta, para la tabla principal
function detailFormatter(index, row) {
    var detalleHtml = `
        <div class="table-detail">
            <h5 class="detail-heading">Detalles del pedido</h5>
            <table class="table table-bordered detail-table">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Submonto</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (row.detalle_pedido) {
        row.detalle_pedido.forEach(function (detalle, index) {
            // Alternar colores de fondo para filas
            var backgroundColor = index % 2 === 0 ? '#f5f5f5' : '#ffffff';

            detalleHtml += `
                <tr style="background-color: ${backgroundColor};">
                    <td>${detalle.item_menu.id_item_menu}</td>
                    <td>${detalle.item_menu.nombre}</td>
                    <td>${detalle.cantidad}</td>
                    <td>${detalle.item_menu.precio}</td>
                    <td>${detalle.sub_monto}</td>
                </tr>
            `;
        });
    }

    detalleHtml += `
                </tbody>
            </table>
        </div>
    `;

    return detalleHtml;
}

function rowStyle(row, index) {
    var estadoPedido = row.estado_pedido;
    var estilo = null;

    switch (estadoPedido) {
        case 'Pendiente':
            estilo = 'bg-warning';
            break;
        case 'Confirmado':
            estilo = 'bg-info';
            break;
        case 'Entregado':
            estilo = 'bg-success';
            break;
        case 'Rechazado':
            estilo = 'bg-danger';
            break;
    }

    // Devuelve siempre un objeto, incluso si el estilo es null
    return { classes: estilo };
}


