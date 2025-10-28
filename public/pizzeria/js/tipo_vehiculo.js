let tipoMenu = [];
let table = $("#tabla-tipo-vehiculo");
let tableEliminados = $("#tabla-tipo-vehiculo-eliminados");

$(document).ready( () => {
    cargarTipoVehiculo();
});

$("#btn-nuevo-tipo-vehiculo").click(() => {
    $("#modal-nuevo-tipo-vehiculo").modal('show');
});

$("#guardar-tipo-vehiculo").click(() => {
    if (validar($("#nombre"))) {
        saveTipoVehiculo();
    } 
});

$(document).on("click", "#actualizar-tipo-vehiculo", function(e) {
    const id_tipo_vehiculo = this.name;
    updateTipoVehiculo(id_tipo_vehiculo);
});

$(document).on("click", ".edit", function() {
    const id_tipo_vehiculo = $(this).attr("data-edit");

    const tipoVehiculoEdit = tipoMenu.find((element) => {
        return element.id_tipo_vehiculo == id_tipo_vehiculo;
    });

    $("#nombre-edit").val(tipoVehiculoEdit.nombre);
    $("#actualizar-tipo-vehiculo").attr("name", id_tipo_vehiculo);
    $("#modal-editar-tipo-vehiculo").modal('show');
});


$(document).on("click", ".delete", function() {
    const id_tipo_vehiculo = $(this).attr("data-delete");
    alertify.confirm("¿Está seguro de eliminar este registro?", "Se borrará el registro",
    function() {
        deleteTipoVehiculo(id_tipo_vehiculo);
    },
    function() {
        alertify.error('Cancelado');
    });
});

$(document).on("click", ".restore", function() {
    const id_tipo_vehiculo = $(this).attr("data-restore");
    alertify.confirm("¿Está seguro de restaurar este registro?", "Se restaurará el registro",
    function() {
        restoreTipoVehiculo(id_tipo_vehiculo);
    },
    function() {
        alertify.error('Cancelado');
    });
});

function cargarTipoVehiculo() {
    const url = rutaApiRest + "tipo-vehiculo";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            tipoMenu = response.data;
            tipoMenu.forEach(element => {
                element.acciones = 
                        `
                        <a data-edit="${element.id_tipo_vehiculo}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                        <a data-delete="${element.id_tipo_vehiculo}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>
                        `;
            });

            table.bootstrapTable('load', tipoMenu);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
    cargarTipoVehiculoEliminados();
}

function saveTipoVehiculo() {
    const data = {};
    data.nombre = $("#nombre").val();
    const url = rutaApiRest + "tipo-vehiculo";
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
                $("#modal-nuevo-tipo-vehiculo").modal('hide');
                cargarTipoVehiculo();
            } else {
                alertify.alert(
                    "Correcto",
                    "Error, ocurrio un problema!"
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


function updateTipoVehiculo(id) {
    const data = {};
    data.nombre = $("#nombre-edit").val();
    const url = rutaApiRest + "tipo-vehiculo/" + id;
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
                $("#modal-editar-tipo-vehiculo").modal('hide');
                cargarTipoVehiculo();
            } else {
                alertify.alert(
                    "Correcto",
                    "Error, ocurrio un problema!"
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

function deleteTipoVehiculo(id) {
    const url = rutaApiRest + "tipo-vehiculo/" + id;
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

                cargarTipoVehiculo();
            } else {
                alertify.alert(
                    "Correcto",
                    "Error, ocurrio un problema!"
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

function cargarTipoVehiculoEliminados() {
    const url = rutaApiRest + "tipo-vehiculo-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            const data = response.data;
            console.log(response);
            data.forEach(element => {
                element.acciones = 
                        `<a data-restore="${element.id_tipo_vehiculo}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
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

function restoreTipoVehiculo(id) {
    const url = rutaApiRest + "tipo-vehiculo-restaurar/" + id;
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

                cargarTipoVehiculo();
                cargarTipoVehiculoEliminados();
            } else {
                alertify.alert(
                    "Correcto",
                    "Error, ocurrio un problema!"
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

