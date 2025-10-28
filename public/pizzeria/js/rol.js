let roles = [];
let table = $("#tabla-rol");
let tableEliminados = $("#tabla-rol-eliminados");

$(document).ready( () => {
    cargarRol();
});

$("#btn-nuevo-rol").click(() => {
    $("#modal-nuevo-rol").modal('show');
});

$("#guardar-rol").click(() => {
    if (validar($("#nombre"))) {
        saveRol();
    } 
});

$(document).on("click", "#actualizar-rol", function(e) {
    const id_rol = this.name;
    updateRol(id_rol);
});

$(document).on("click", ".edit", function() {
    const id_rol = $(this).attr("data-edit");

    const rolEdit = roles.find((element) => {
        return element.id_rol == id_rol;
    });

    $("#nombre-edit").val(rolEdit.nombre);
    $("#actualizar-rol").attr("name", id_rol);
    $("#modal-editar-rol").modal('show');
});


$(document).on("click", ".delete", function() {
    const id_rol = $(this).attr("data-delete");
    alertify.confirm("¿Está seguro de eliminar este registro?", "Se borrará el registro",
    function() {
        deleteRol(id_rol);
    },
    function() {
        alertify.error('Cancelado');
    });
});

$(document).on("click", ".restore", function() {
    const id_rol = $(this).attr("data-restore");
    alertify.confirm("¿Está seguro de restaurar este registro?", "Se restaurará el registro",
    function() {
        restoreRol(id_rol);
    },
    function() {
        alertify.error('Cancelado');
    });
});

function cargarRol() {
    const url = rutaApiRest + "rol";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            roles = response.data;
            roles.forEach(element => {
                element.acciones = 
                        `
                        <a data-edit="${element.id_rol}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                        <a data-delete="${element.id_rol}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>
                        `;
            });

            table.bootstrapTable('load', roles);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
    cargarRolEliminados();
}

function saveRol() {
    const data = {};
    data.nombre = $("#nombre").val();
    const url = rutaApiRest + "rol";
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
                $("#modal-nuevo-rol").modal('hide');
                cargarRol();
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


function updateRol(id) {
    const data = {};
    data.nombre = $("#nombre-edit").val();
    const url = rutaApiRest + "rol/" + id;
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
                $("#modal-editar-rol").modal('hide');
                cargarRol();
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

function deleteRol(id) {
    const url = rutaApiRest + "rol/" + id;
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

                cargarRol();
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

function cargarRolEliminados() {
    const url = rutaApiRest + "rol-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            const data = response.data;
            console.log(response);
            data.forEach(element => {
                element.acciones = 
                        `<a data-restore="${element.id_rol}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
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

function restoreRol(id) {
    const url = rutaApiRest + "rol-restaurar/" + id;
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

                cargarRol();
                cargarRolEliminados();
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

