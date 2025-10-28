let itemMenu = [];
let tipoMenu = [];
let itemMenuEliminados = [];
let table = $("#tabla-item-menu");
let tableEliminados = $("#tabla-item-menu-eliminados");

$(document).ready(() => {
    cargarItemMenu();
    cargarTipoMenu();
});

$("#btn-nuevo-item-menu").click(() => {
    $("#modal-nuevo-item-menu").modal('show');
});

$("#guardar-item-menu").click(() => {
    if (validar($("#nombre")) && 
        validar($("#precio")) && 
        validar($("#descripcion"))) {
        saveItemMenu();
    } 
});

$("#actualizar-item-menu").click(() => {
    if (validar($("#nombre-edit")) && 
        validar($("#precio-edit")) && 
        validar($("#descripcion-edit"))) {
        const id_item_menu = $("#actualizar-item-menu").attr('name');
        updateItemMenu(id_item_menu);
    }  
});

$(document).on("click", ".edit", function() {
    const id_item_menu = $(this).attr("data-edit");

    const itemMenuEdit = itemMenu.find((element) => {
        return element.id_item_menu == id_item_menu;
    });
    
    $("#nombre-edit").val(itemMenuEdit.nombre);
    $("#precio-edit").val(itemMenuEdit.precio);
    $("#descripcion-edit").val(itemMenuEdit.descripcion);
    $("#imagen-edit").attr('src', itemMenuEdit.imagen);
    $("#actualizar-item-menu").attr("name", id_item_menu);
    $("#modal-edit-item-menu").modal('show');
    vistaPreviaEdit();
    cargarSelect(tipoMenu, itemMenuEdit.id_tipo_menu, $("#id-tipo-menu-edit"));
});

$(document).on("click", ".delete", function() {
    const id_item_menu = $(this).attr("data-delete");
    alertify.confirm("¿Está seguro de eliminar este registro?", "Se borrará el registro",
    function() {
        deleteItemMenu(id_item_menu);
    },
    function() {
        alertify.error('Cancelado');
    });
});


$(document).on("click", ".restore", function() {
    const id_item_menu = $(this).attr("data-restore");
    alertify.confirm("Restaurar", "Se restaurará el registro",
    function() {
        restoreItemMenu(id_item_menu);
    },
    function() {
        alertify.error('Cancelado');
    });
});


function cargarItemMenu() {
    const url = rutaApiRest + "item-menu";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            itemMenu = response.data;
            cargarTablaItemMenu(itemMenu, false, table);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
    cargarItemMenuEliminados();
}

function cargarTablaItemMenu(itemMenu, eliminados = false, table) {
    const itemMenuObject = [];
    itemMenu.forEach(element => {
        const object = {};
        object.id_item_menu = element.id_item_menu;
        object.nombre = element.nombre;
        object.precio = element.precio;
        object.descripcion = element.descripcion;
        object.tipo_menu = element.tipo_menu.nombre;
        object.imagen_td = `<img src="${ rutaLocal + element.imagen}" class="imagen">`;

        const accionRestarurar = `<a data-restore="${element.id_item_menu}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
        const accionIndex = `<a data-edit="${element.id_item_menu}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                             <a data-delete="${element.id_item_menu}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>`;
        
        object.acciones = eliminados == true ? accionRestarurar : accionIndex;
                        
        itemMenuObject.push(object);
    });

    table.bootstrapTable('load', itemMenuObject);
}

function saveItemMenu() {
    const formData = new FormData();

    formData.append('nombre', $("#nombre").val());
    formData.append('precio', $("#precio").val());
    formData.append('descripcion', $("#descripcion").val());
    formData.append('id_tipo_menu', $("#id-tipo-menu").val());

    const imagenInput = $("#imagen")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "item-menu";
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false, 
        processData: false,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se insertó correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                cargarItemMenu();
                limpiarInput();
            } else {
                alertify.alert(
                    "Error",
                    "¡Ocurrió un problema!"
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


function updateItemMenu(id) {
    const formData = new FormData();

    formData.append('nombre', $("#nombre-edit").val());
    formData.append('precio', $("#precio-edit").val());
    formData.append('descripcion', $("#descripcion-edit").val());
    formData.append('id_tipo_menu', $("#id-tipo-menu-edit").val());

    const imagenInput = $("#imagen-edit")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "item-menu/" + id;
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se actualizó correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                cargarItemMenu();
                limpiarInput();
            } else {
                alertify.alert(
                    "Error",
                    "¡Ocurrió un problema!"
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


function deleteItemMenu(id) {
    const url = rutaApiRest + "item-menu/" + id;
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

                cargarItemMenu();
            } else {
                alertify.alert(
                    "Error",
                    "¡Ocurrio un problema!"
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

function cargarItemMenuEliminados() {
    const url = rutaApiRest + "item-menu-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            itemMenuEliminados = response.data;
            console.log(response);
            cargarTablaItemMenu(itemMenuEliminados, true, tableEliminados)

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function restoreItemMenu(id) {
    const url = rutaApiRest + "item-menu-restaurar/" + id;
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

                cargarItemMenu();
                cargarItemMenuEliminados();
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

function limpiarInput() {
    $("#nombre").val("");
    $("#precio").val("");
    $("#descripcion").val("");
    $("#modal-nuevo-item-menu").modal('hide');

    $("#nombre-edit").val("");
    $("#precio-edit").val("");
    $("#descripcion-edit").val("");
    $("#modal-edit-item-menu").modal('hide');
}

function cargarTipoMenu() {
    const url = rutaApiRest + "tipo-menu";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            tipoMenu = response.data;
            const idTipoMenu = 0;
            const selectGuardar = $("#id-tipo-menu");
            const selectEditar = $("#id-tipo-menu-edit");
            cargarSelect(tipoMenu, idTipoMenu, selectGuardar);
            cargarSelect(tipoMenu, idTipoMenu, selectEditar);
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}

function cargarSelect(array, id = 0, select) {
    select.select2({width: '100%', theme: "classic"});
    select.empty();
    array.forEach(element => {
        let selected = "";
        if (id == element.id_tipo_menu) {
            selected = "selected";
        }
        select.append(
            `<option value="${element.id_tipo_menu}" ${selected}>${element.nombre}</option>`
          );
    });
}

function mostrarVistaPrevia() {
    const inputImagen = document.getElementById('imagen');
    const vistaPrevia = document.getElementById('vista-previa');

    const archivo = inputImagen.files[0];

    if (archivo) {
        const lector = new FileReader();

        lector.onload = function(e) {
            vistaPrevia.src = e.target.result;
            vistaPrevia.style.display = 'block';
        };

        lector.readAsDataURL(archivo);
    } else {
        vistaPrevia.style.display = 'none';
        vistaPrevia.src = '';  
    }
}

function mostrarVistaPreviaEdit() {
    const inputImagen = document.getElementById('imagen-edit');
    const vistaPrevia = document.getElementById('vista-previa-edit');

    const archivo = inputImagen.files[0];

    if (archivo) {
        const lector = new FileReader();

        lector.onload = function(e) {
            vistaPrevia.src = e.target.result;
            vistaPrevia.style.display = 'block';
        };

        lector.readAsDataURL(archivo);
    } else {
        vistaPrevia.style.display = 'none';
        vistaPrevia.src = '';  
    }
}

function vistaPreviaEdit() {
    try {
        const inputImagen = document.getElementById('imagen-edit');
        const vistaPrevia = document.getElementById('vista-previa-edit');

        vistaPrevia.src = inputImagen.src;
        vistaPrevia.style.display = 'block';
    } catch (error) {
        
    }
}

