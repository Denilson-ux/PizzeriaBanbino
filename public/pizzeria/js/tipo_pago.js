let tipoPago = [];
let table = $("#tabla-tipo-pago");
let tableEliminados = $("#tabla-tipo-pago-eliminados");

$(document).ready( () => {
    cargarTipoPago();
});

$("#btn-nuevo-tipo-pago").click(() => {
    $("#modal-nuevo-tipo-pago").modal('show');
});

$("#guardar-tipo-pago").click(() => {
    if (validar($("#nombre"))  &&  validar($("#descripcion"))) {
        saveTipoPago();
    } 
});

$(document).on("click", "#actualizar-tipo-pago", function(e) {
    const id_tipo_pago = this.name;
    updateTipoPago(id_tipo_pago);
});

$(document).on("click", ".edit", function() {
    const id_tipo_pago = $(this).attr("data-edit");

    const tipoPagoEdit = tipoPago.find((element) => {
        return element.id_tipo_pago == id_tipo_pago;
    });

    $("#nombre-edit").val(tipoPagoEdit.nombre);
    $("#descripcion-edit").val(tipoPagoEdit.descripcion);
    $("#actualizar-tipo-pago").attr("name", id_tipo_pago);
    $("#modal-editar-tipo-pago").modal('show');
});


$(document).on("click", ".delete", function() {
    const id_tipo_pago = $(this).attr("data-delete");
    alertify.confirm("Eliminar", "¿Está seguro de eliminar este registro?",
    function() {
        deleteTipoPago(id_tipo_pago);
    },
    function() {
        alertify.error('Cancelado');
    });
});

$(document).on("click", ".restore", function() {
    const id_tipo_pago = $(this).attr("data-restore");
    alertify.confirm("Restaurar", "¿Está seguro de restaurar este registro?",
    function() {
        restoreTipoPago(id_tipo_pago);
    },
    function() {
        alertify.error('Cancelado');
    });
});

function cargarTipoPago() {
    const url = rutaApiRest + "tipo-pago";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            tipoPago = response.data;
            tipoPago.forEach(element => {
                element.acciones = 
                        `
                        <a data-edit="${element.id_tipo_pago}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                        <a data-delete="${element.id_tipo_pago}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>
                        `;
            });

            table.bootstrapTable('load', tipoPago);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
    cargarTipoPagoEliminados();
}

function saveTipoPago() {
    const data = {};
    data.nombre = $("#nombre").val();
    data.descripcion = $("#descripcion").val();
    const url = rutaApiRest + "tipo-pago";
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: data,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se insertó correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                $("#nombre").val("");
                $("#modal-nuevo-tipo-pago").modal('hide');
                cargarTipoPago();
            } else {
                alertify.alert(
                    "Error",
                    "Ocurrio un problema!"
                );
            }
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}


function updateTipoPago(id) {
    const data = {};
    data.nombre = $("#nombre-edit").val();
    data.descripcion = $("#descripcion-edit").val();
    const url = rutaApiRest + "tipo-pago/" + id;
    $.ajax({
        url: url,
        type: "PUT",
        dataType: "json",
        data: data,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert(
                    "Correcto",
                    "¡Súper, se actualizó correctamente!"
                );
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                $("#nombre-edit").val("");
                $("#modal-editar-tipo-pago").modal('hide');
                cargarTipoPago();
            } else {
                alertify.alert(
                    "Error",
                    "Ocurrió un problema!"
                );
            }
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}

function deleteTipoPago(id) {
    const url = rutaApiRest + "tipo-pago/" + id;
    $.ajax({
        url: url,
        type: "DELETE",
        dataType: "json",
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert(
                    "Correcto",
                    "¡Súper, se eliminó correctamente!"
                );
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                cargarTipoPago();
            } else {
                alertify.alert(
                    "Error",
                    "Ocurrió un problema!"
                );
            }
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}

function cargarTipoPagoEliminados() {
    const url = rutaApiRest + "tipo-pago-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            const data = response.data;
            console.log(response);
            data.forEach(element => {
                element.acciones = 
                        `<a data-restore="${element.id_tipo_pago}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
            });

            tableEliminados.bootstrapTable('load', data);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}

function restoreTipoPago(id) {
    const url = rutaApiRest + "tipo-pago-restaurar/" + id;
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert(
                    "Correcto",
                    "¡Súper, se restauró correctamente!"
                );
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                cargarTipoPago();
                cargarTipoPagoEliminados();
            } else {
                alertify.alert(
                    "Error",
                    "Ups, ocurrio un problema!"
                );
            }
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}

