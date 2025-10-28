let notaVentas = [];
let table = $("#tabla-nota-venta");
let tableEliminados = $("#tabla-rol-eliminados");

$(document).ready( () => {
    cargarNotaVenta();
});

$(document).on("click", ".print", function() {
    const idNotaVenta = $(this).attr("data-id");
    window.location.href = "nota-venta-comprobante-pdf/" + idNotaVenta;
    // window.open("nota-venta-comprobante-pdf/" + idNotaVenta, "_blank");
});


function cargarNotaVenta() {
    showLoader();
    const url = rutaApiRest + "nota-venta";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            notaVentas = response.data;

            notaVentas.forEach(element => {
                element.descuento = element.cliente.descuento;
                element.cliente_td = element.cliente.persona.nombre;
                element.empleado_td = element.empleado.persona.nombre;
                element.metodo_pago = element.tipo_pago.nombre;
                element.acciones = 
                        `
                        <a data-id="${element.id_nota_venta}" class="btn btn-primary btn-sm print" title="Imprimir"><i class="fa fa-print"></i></a>
                        `;
            });

            cargarItemMenu();
            table.bootstrapTable('load', notaVentas);

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

//detalle venta, para la tabla principal
function detailFormatter(index, row) {
    let montoTotal = 0;
    var detalleHtml = `
        <div class="table-detail">
            <h5 class="detail-heading">Detalles de venta</h5>
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

    if (row.detalle_venta) {
        row.detalle_venta.forEach(function (detalle, index) {
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

            montoTotal += parseFloat(detalle.sub_monto);
        });
    }

    detalleHtml += `
        <tr>
            <td colspan="4" style="text-align: right;">Total:</td>
            <td>${montoTotal}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;">Descuento %:</td>
            <td>${row.descuento}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align: right;">Total a pagar:</td>
            <td>${row.monto}</td>
        </tr>
    `;

    return detalleHtml;
}

function cargarItemMenu() {
    const url = rutaApiRest + "item-menu";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            const itemsMenu = response.data;

            //Construir el objeto de notaVentas con el detalle + itemMenu
            notaVentas.forEach(element => {
                const detalleVenta = element.detalle_venta;
                detalleVenta.forEach(element2 => {
                    element2.item_menu = itemsMenu.find(item => item.id_item_menu == element2.id_item_menu);
                });
            });

            console.log(notaVentas);
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}
