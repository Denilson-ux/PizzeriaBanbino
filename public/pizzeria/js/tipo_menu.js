let tipoMenu = [];
let table = $("#tabla-tipo-menu");
let tableEliminados = $("#tabla-tipo-menu-eliminados");

$(document).ready( () => {
    cargarTipoMenu();
});

$("#btn-nuevo-tipo-menu").click(() => {
    $("#modal-nuevo-tipo-menu").modal('show');
});

$("#guardar-tipo-menu").click(() => {
    if (validar($("#nombre"))) {
        saveItemTipoMenu();
    } 
});

$(document).on("click", "#actualizar-tipo-menu", function(e) {
    const id_tipo_menu = this.name;
    updateItemTipoMenu(id_tipo_menu);
});

$(document).on("click", ".edit", function() {
    const id_tipo_menu = $(this).attr("data-edit");

    const tipoMenuEdit = tipoMenu.find((element) => {
        return element.id_tipo_menu == id_tipo_menu;
    });

    $("#nombre-edit").val(tipoMenuEdit.nombre);
    $("#actualizar-tipo-menu").attr("name", id_tipo_menu);
    $("#modal-editar-tipo-menu").modal('show');
});


$(document).on("click", ".delete", function() {
    const id_tipo_menu = $(this).attr("data-delete");
    alertify.confirm("¿Está seguro de eliminar este registro?", "Se borrará el registro",
    function() {
        deleteItemTipoMenu(id_tipo_menu);
    },
    function() {
        alertify.error('Cancelado');
    });
});

$(document).on("click", ".restore", function() {
    const id_tipo_menu = $(this).attr("data-restore");
    alertify.confirm("¿Está seguro de restaurar este registro?", "Se restaurará el registro",
    function() {
        restoreItemTipoMenu(id_tipo_menu);
    },
    function() {
        alertify.error('Cancelado');
    });
});

function cargarTipoMenu() {
    const url = rutaApiRest + "tipo-menu";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            tipoMenu = response.data;
            tipoMenu.forEach(element => {
                element.acciones = 
                        `
                        <a data-edit="${element.id_tipo_menu}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                        <a data-delete="${element.id_tipo_menu}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>
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
    cargarTipoMenuEliminados();
}

function saveItemTipoMenu() {
    const data = {};
    data.nombre = $("#nombre").val();
    const url = rutaApiRest + "tipo-menu";
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
                $("#modal-nuevo-tipo-menu").modal('hide');
                cargarTipoMenu();
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


function updateItemTipoMenu(id) {
    const data = {};
    data.nombre = $("#nombre-edit").val();
    const url = rutaApiRest + "tipo-menu/" + id;
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
                $("#modal-editar-tipo-menu").modal('hide');
                cargarTipoMenu();
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

function deleteItemTipoMenu(id) {
    const url = rutaApiRest + "tipo-menu/" + id;
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

                cargarTipoMenu();
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

function cargarTipoMenuEliminados() {
    const url = rutaApiRest + "tipo-menu-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            const data = response.data;
            console.log(response);
            data.forEach(element => {
                element.acciones = 
                        `<a data-restore="${element.id_tipo_menu}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
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

function restoreItemTipoMenu(id) {
    const url = rutaApiRest + "tipo-menu-restaurar/" + id;
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

                cargarTipoMenu();
                cargarTipoMenuEliminados();
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

