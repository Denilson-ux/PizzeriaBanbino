let empleados = [];
let empleadosEliminados = [];
let table = $("#tabla-empleado");
let tableEliminados = $("#tabla-empleado-eliminados");

$(document).ready(() => {
    cargarEmpleado();
});

$("#btn-nuevo-empleado").click(() => {
    $("#modal-nuevo-empleado").modal('show');
});

$("#guardar-empleado").click(() => {
    if (validar($("#nombre")) && 
        validar($("#paterno")) && 
        validar($("#telefono")) && 
        validar($("#correo"))) {
        saveEmpleado();
    } 
});

$("#actualizar-empleado").click(() => {
    if (validar($("#nombre-edit")) && 
        validar($("#paterno-edit")) && 
        validar($("#telefono-edit")) && 
        validar($("#correo-edit"))) {
        const id_empleado = $("#actualizar-empleado").attr('name');
        updateEmpleado(id_empleado);
    }  
});

$(document).on("click", ".edit", function() {
    const id_empleado = $(this).attr("data-edit");

    const empleadoEdit = empleados.find((element) => {
        return element.id_empleado == id_empleado;
    });
    
    const personaEdit = empleadoEdit.persona;
    
    $("#sueldo-edit").val(empleadoEdit.sueldo);
    $("#nombre-edit").val(personaEdit.nombre);
    $("#paterno-edit").val(personaEdit.paterno);
    $("#materno-edit").val(personaEdit.materno);
    $("#telefono-edit").val(personaEdit.telefono);
    $("#correo-edit").val(personaEdit.correo);
    $("#imagen-edit").attr('src', personaEdit.imagen);
    vistaPreviaEdit();
    $("#actualizar-empleado").attr("name", id_empleado);
    $("#modal-edit-empleado").modal('show');
});

$(document).on("click", ".delete", function() {
    const id_empleado = $(this).attr("data-delete");
    alertify.confirm("¿Está seguro de eliminar este registro?", "Se borrará el registro",
    function() {
        deleteEmpleado(id_empleado);
    },
    function() {
        alertify.error('Cancelado');
    });
});


$(document).on("click", ".restore", function() {
    const id_tipo_menu = $(this).attr("data-restore");
    alertify.confirm("¿Está seguro de restaurar este registro?", "Se restaurará el registro",
    function() {
        restoreEmpleado(id_tipo_menu);
    },
    function() {
        alertify.error('Cancelado');
    });
});


function cargarEmpleado() {
    const url = rutaApiRest + "empleado";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            empleados = response.data;
            cargarTablaEmpleado(empleados, false, table);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
    cargarEmpleadoEliminados();
}

function cargarTablaEmpleado(empleados, eliminados = false, table) {
    const empleadoPersona = [];
    empleados.forEach(empleado => {
        const object = {};
        const persona = empleado.persona;
        object.id_empleado = empleado.id_empleado;
        object.sueldo = empleado.sueldo;
        object.compras_realizadas = empleado.compras_realizadas;
        object.nombre = persona.nombre;
        object.paterno = persona.paterno;
        object.materno = persona.materno;
        object.telefono = persona.telefono;
        object.correo = persona.correo;
        object.imagen_td = `<img src="${ rutaLocal + persona.imagen}" class="imagen">`;

        const accionRestarurar = `<a data-restore="${empleado.id_empleado}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
        const accionIndex = `<a data-edit="${empleado.id_empleado}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                             <a data-delete="${empleado.id_empleado}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>`;
        
        object.acciones = eliminados == true ? accionRestarurar : accionIndex;
                        
        empleadoPersona.push(object);
    });

    table.bootstrapTable('load', empleadoPersona);
}

function saveEmpleado() {
    const formData = new FormData();

    formData.append('nombre', $("#nombre").val());
    formData.append('paterno', $("#paterno").val());
    formData.append('materno', $("#materno").val());
    formData.append('telefono', $("#telefono").val());
    formData.append('correo', $("#correo").val());
    formData.append('sueldo', $("#sueldo").val());

    const imagenInput = $("#imagen")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "empleado";

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

                cargarEmpleado();
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


function updateEmpleado(id) {
    const formData = new FormData();

    formData.append('nombre', $("#nombre-edit").val());
    formData.append('paterno', $("#paterno-edit").val());
    formData.append('materno', $("#materno-edit").val());
    formData.append('telefono', $("#telefono-edit").val());
    formData.append('correo', $("#correo-edit").val());
    formData.append('sueldo', $("#sueldo-edit").val());

    const imagenInput = $("#imagen-edit")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "empleado/" + id;

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

                cargarEmpleado();
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


function deleteEmpleado(id) {
    const url = rutaApiRest + "empleado/" + id;
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

                cargarEmpleado();
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

function cargarEmpleadoEliminados() {
    const url = rutaApiRest + "empleado-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            empleadosEliminados = response.data;
            console.log(response);
            cargarTablaEmpleado(empleadosEliminados, true, tableEliminados)

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function restoreEmpleado(id) {
    const url = rutaApiRest + "empleado-restaurar/" + id;
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

                cargarEmpleado();
                cargarEmpleadoEliminados();
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
    $("#sueldo").val("0");
    $("#modal-nuevo-empleado").modal('hide');

    $("#nombre-edit").val("");
    $("#paterno-edit").val("");
    $("#materno-edit").val("");
    $("#telefono-edit").val("");
    $("#correo-edit").val("");
    $("#sueldo-edit").val("0");
    $("#modal-edit-empleado").modal('hide');
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

