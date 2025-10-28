let clientes = [];
let clientesEliminados = [];
let table = $("#tabla-cliente");
let tableEliminados = $("#tabla-cliente-eliminados");

$(document).ready(() => {
    cargarCliente();
});

$("#btn-nuevo-cliente").click(() => {
    $("#modal-nuevo-cliente").modal('show');
});

$("#guardar-cliente").click(() => {
    if (validar($("#nombre")) && 
        validar($("#paterno")) && 
        validar($("#telefono")) && 
        validar($("#correo"))) {
        saveCliente();
    } 
});

$("#actualizar-cliente").click(() => {
    if (validar($("#nombre-edit")) && 
        validar($("#paterno-edit")) && 
        validar($("#telefono-edit")) && 
        validar($("#correo-edit"))) {
        const id_cliente = $("#actualizar-cliente").attr('name');
        updateCliente(id_cliente);
    }  
});

$(document).on("click", ".edit", function() {
    const id_cliente = $(this).attr("data-edit");

    const clienteEdit = clientes.find((element) => {
        return element.id_cliente == id_cliente;
    });
    
    const personaEdit = clienteEdit.persona;
    
    $("#descuento-edit").val(clienteEdit.descuento);
    $("#nombre-edit").val(personaEdit.nombre);
    $("#paterno-edit").val(personaEdit.paterno);
    $("#materno-edit").val(personaEdit.materno);
    $("#telefono-edit").val(personaEdit.telefono);
    $("#correo-edit").val(personaEdit.correo);
    $("#actualizar-cliente").attr("name", id_cliente);
    $("#modal-edit-cliente").modal('show');
});

$(document).on("click", ".delete", function() {
    const id_cliente = $(this).attr("data-delete");
    alertify.confirm("¿Está seguro de eliminar este registro?", "Se borrará el registro",
    function() {
        deleteCliente(id_cliente);
    },
    function() {
        alertify.error('Cancelado');
    });
});


$(document).on("click", ".restore", function() {
    const id_tipo_menu = $(this).attr("data-restore");
    alertify.confirm("¿Está seguro de restaurar este registro?", "Se restaurará el registro",
    function() {
        restoreCliente(id_tipo_menu);
    },
    function() {
        alertify.error('Cancelado');
    });
});


function cargarCliente() {
    const url = rutaApiRest + "cliente";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            clientes = response.data;
            cargarTablaCliente(clientes, false, table);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
    cargarClienteEliminados();
}

function cargarTablaCliente(clientes, eliminados = false, table) {
    const clientePersona = [];
    clientes.forEach(cliente => {
        const object = {};
        const persona = cliente.persona;
        object.id_cliente = cliente.id_cliente;
        object.descuento = cliente.descuento;
        object.compras_realizadas = cliente.compras_realizadas;
        object.nombre = persona.nombre;
        object.paterno = persona.paterno;
        object.materno = persona.materno;
        object.telefono = persona.telefono;
        object.correo = persona.correo;

        const accionRestarurar = `<a data-restore="${cliente.id_cliente}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
        const accionIndex = `<a data-edit="${cliente.id_cliente}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                             <a data-delete="${cliente.id_cliente}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>`;
        
        object.acciones = eliminados == true ? accionRestarurar : accionIndex;
                        
        clientePersona.push(object);
    });

    table.bootstrapTable('load', clientePersona);
}

function saveCliente() {
    const data = {};
    data.nombre = $("#nombre").val();
    data.paterno = $("#paterno").val();
    data.materno = $("#materno").val();
    data.telefono = $("#telefono").val();
    data.correo = $("#correo").val();
    data.descuento = $("#descuento").val();
    data.compras_realizadas = 0; //Default
    const url = rutaApiRest + "cliente";
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

                cargarCliente();
                limpiarInput();
                cargarClientesAsync(); //Metodo que viene de create_nota_venta.js
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

function updateCliente(id) {
    const data = {};
    data.nombre = $("#nombre-edit").val();
    data.paterno = $("#paterno-edit").val();
    data.materno = $("#materno-edit").val();
    data.telefono = $("#telefono-edit").val();
    data.correo = $("#correo-edit").val();
    data.descuento = $("#descuento-edit").val();
    data.compras_realizadas = 0; //Default
    const url = rutaApiRest + "cliente/" + id;
    $.ajax({
        url: url,
        type: "PUT",
        dataType: "json",
        data: data,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se actualizó correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                cargarCliente();
                limpiarInput();
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

function deleteCliente(id) {
    const url = rutaApiRest + "cliente/" + id;
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

                cargarCliente();
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

function cargarClienteEliminados() {
    const url = rutaApiRest + "cliente-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            clientesEliminados = response.data;
            console.log(response);

            cargarTablaCliente(clientesEliminados, true, tableEliminados)

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function restoreCliente(id) {
    const url = rutaApiRest + "cliente-restaurar/" + id;
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

                cargarCliente();
                cargarClienteEliminados();
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
    $("#paterno").val("");
    $("#materno").val("");
    $("#telefono").val("");
    $("#correo").val("");
    $("#descuento").val("0");
    $("#modal-nuevo-cliente").modal('hide');

    $("#nombre-edit").val("");
    $("#paterno-edit").val("");
    $("#materno-edit").val("");
    $("#telefono-edit").val("");
    $("#correo-edit").val("");
    $("#descuento-edit").val("0");
    $("#modal-edit-cliente").modal('hide');
}
