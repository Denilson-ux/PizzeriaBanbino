let repartidores = [];
let repartidoresEliminados = [];
let table = $("#tabla-repartidor");
let tableEliminados = $("#tabla-repartidor-eliminados");

$(document).ready(() => {
    cargarRepartidor();
});

$("#btn-nuevo-repartidor").click(() => {
    $("#modal-nuevo-repartidor").modal('show');
});

$("#guardar-repartidor").click(() => {
    if (validar($("#nombre")) && 
        validar($("#paterno")) && 
        validar($("#telefono")) && 
        validar($("#correo")) &&
        validar($("#licencia_conducir"))) {
        saveRepartidor();
    } 
});

$("#actualizar-repartidor").click(() => {
    if (validar($("#nombre-edit")) && 
        validar($("#paterno-edit")) && 
        validar($("#telefono-edit")) && 
        validar($("#correo-edit")) && 
        validar($("#licencia_conducir"))) {
        const id_repartidor = $("#actualizar-repartidor").attr('name');
        updateRepartidor(id_repartidor);
    }  
});

$(document).on("click", ".edit", function() {
    const id_repartidor = $(this).attr("data-edit");

    const repartidorEdit = repartidores.find((element) => {
        return element.id_repartidor == id_repartidor;
    });
    
    const personaEdit = repartidorEdit.persona;
    
    $("#licencia_conducir-edit").val(repartidorEdit.licencia_conducir);
    $("#nombre-edit").val(personaEdit.nombre);
    $("#paterno-edit").val(personaEdit.paterno);
    $("#materno-edit").val(personaEdit.materno);
    $("#telefono-edit").val(personaEdit.telefono);
    $("#correo-edit").val(personaEdit.correo);
    $("#imagen-edit").attr('src', personaEdit.imagen);
    vistaPreviaEdit();
    $("#actualizar-repartidor").attr("name", id_repartidor);
    $("#modal-edit-repartidor").modal('show');
});

$(document).on("click", ".delete", function() {
    const id_repartidor = $(this).attr("data-delete");
    alertify.confirm("¿Está seguro de eliminar este registro?", "Se borrará el registro",
    function() {
        deleteRepartidor(id_repartidor);
    },
    function() {
        alertify.error('Cancelado');
    });
});


$(document).on("click", ".restore", function() {
    const id_repartidor = $(this).attr("data-restore");
    alertify.confirm("¿Está seguro de restaurar este registro?", "Se restaurará el registro",
    function() {
        restoreRepartidor(id_repartidor);
    },
    function() {
        alertify.error('Cancelado');
    });
});


function cargarRepartidor() {
    const url = rutaApiRest + "repartidor";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            repartidores = response.data;
            cargarTablaRepartidor(repartidores, false, table);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
    cargarRepartidorEliminados();
}

function cargarTablaRepartidor(repartidores, eliminados = false, table) {
    const empleadoPersona = [];
    repartidores.forEach(repartidor => {
        const object = {};
        const persona = repartidor.persona;
        object.id_repartidor = repartidor.id_repartidor;
        object.licencia_conducir = repartidor.licencia_conducir;
        object.compras_realizadas = repartidor.compras_realizadas;
        object.nombre = persona.nombre;
        object.paterno = persona.paterno;
        object.materno = persona.materno;
        object.telefono = persona.telefono;
        object.correo = persona.correo;
        object.imagen_td = `<img src="${ rutaLocal + persona.imagen}" class="imagen">`;

        const accionRestarurar = `<a data-restore="${repartidor.id_repartidor}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
        const accionIndex = `<a data-edit="${repartidor.id_repartidor}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                             <a data-delete="${repartidor.id_repartidor}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>`;
        
        object.acciones = eliminados == true ? accionRestarurar : accionIndex;
                        
        empleadoPersona.push(object);
    });

    table.bootstrapTable('load', empleadoPersona);
}

function saveRepartidor() {
    const formData = new FormData();

    formData.append('nombre', $("#nombre").val());
    formData.append('paterno', $("#paterno").val());
    formData.append('materno', $("#materno").val());
    formData.append('telefono', $("#telefono").val());
    formData.append('correo', $("#correo").val());
    formData.append('licencia_conducir', $("#licencia_conducir").val());

    const imagenInput = $("#imagen")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "repartidor";

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

                cargarRepartidor();
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


function updateRepartidor(id) {
    const formData = new FormData();

    formData.append('nombre', $("#nombre-edit").val());
    formData.append('paterno', $("#paterno-edit").val());
    formData.append('materno', $("#materno-edit").val());
    formData.append('telefono', $("#telefono-edit").val());
    formData.append('correo', $("#correo-edit").val());
    formData.append('licencia_conducir', $("#licencia_conducir-edit").val());

    const imagenInput = $("#imagen-edit")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "repartidor/" + id;

    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false, // Don't set contentType to process the data correctly
        processData: false,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se actualizó correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                cargarRepartidor();
                limpiarInput();
            } else {
                alertify.alert(
                    "Correcto",
                    "Error, ocurrió un problema!"
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


function deleteRepartidor(id) {
    const url = rutaApiRest + "repartidor/" + id;
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

                cargarRepartidor();
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

function cargarRepartidorEliminados() {
    const url = rutaApiRest + "repartidor-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            repartidoresEliminados = response.data;
            console.log(response);
            cargarTablaRepartidor(repartidoresEliminados, true, tableEliminados)

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function restoreRepartidor(id) {
    const url = rutaApiRest + "repartidor-restaurar/" + id;
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

                cargarRepartidor();
                cargarRepartidorEliminados();
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
    $("#licencia_conducir").val("0");
    $("#modal-nuevo-repartidor").modal('hide');

    $("#nombre-edit").val("");
    $("#paterno-edit").val("");
    $("#materno-edit").val("");
    $("#telefono-edit").val("");
    $("#correo-edit").val("");
    $("#licencia_conducir-edit").val("0");
    $("#modal-edit-repartidor").modal('hide');
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
    const inputImagen = document.getElementById('imagen-edit');
    const vistaPrevia = document.getElementById('vista-previa-edit');

    vistaPrevia.src = inputImagen.src;
    vistaPrevia.style.display = 'block';
}

