let vehiculo = [];
let vehiculoEliminados = [];
let tipoVehiculo = [];
let repartidores = [];
let table = $("#tabla-vehiculo");
let tableEliminados = $("#tabla-vehiculo-eliminados");
let tableRepartidor = $("#tabla-repartidor");
let idVehiculo = 0;
let idRepartidor = 0;

$(document).ready(() => {
    cargarVehiculo();
    cargarTipoVehiculo();
    cargarRepartidores();
});

$("#btn-nuevo-vehiculo").click(() => {
    $("#modal-nuevo-vehiculo").modal('show');
});

$("#guardar-vehiculo").click(() => {
    if (validar($("#placa")) && 
        validar($("#marca")) && 
        validar($("#modelo")) && 
        validar($("#color"))) {
        saveVehiculo();
    } 
});

$("#actualizar-vehiculo").click(() => {
    if (validar($("#placa-edit")) && 
        validar($("#marca-edit")) && 
        validar($("#modelo-edit")) && 
        validar($("#color-edit"))) {
        const idVehiculo = $("#actualizar-vehiculo").attr('name');
        updateVehiculo(idVehiculo, idRepartidor);
    }  
});

$(document).on("click", ".edit", function() {
    const idVehiculoEdit = $(this).attr("data-edit");

    const vehiculoEdit = vehiculo.find((element) => {
        return element.id_vehiculo == idVehiculoEdit;
    });
    
    $("#placa-edit").val(vehiculoEdit.placa);
    $("#marca-edit").val(vehiculoEdit.marca);
    $("#modelo-edit").val(vehiculoEdit.modelo);
    $("#color-edit").val(vehiculoEdit.color);
    $("#anio-edit").val(vehiculoEdit.anio);
    $("#imagen-edit").attr('src', vehiculoEdit.imagen);
    idRepartidor = vehiculoEdit.id_repartidor;
    $("#actualizar-vehiculo").attr("name", idVehiculoEdit);
    $("#modal-edit-vehiculo").modal('show');
    vistaPreviaEdit();
    cargarSelect(tipoVehiculo, vehiculoEdit.id_tipo_vehiculo, $("#id-tipo-vehiculo-edit"));
});

$(document).on("click", ".delete", function() {
    const idVehiculo = $(this).attr("data-delete");
    alertify.confirm("Eliminar", "¿Está seguro de eliminar este registro?",
    function() {
        deleteVehiculo(idVehiculo);
    },
    function() {
        alertify.error('Cancelado');
    });
});

$(document).on("click", ".asignar", function() {
    idVehiculo = $(this).attr("data-id");

    const vehiculoEdit = vehiculo.find((element) => {
        return element.id_vehiculo == idVehiculo;
    });
    
    $("#placa-edit").val(vehiculoEdit.placa);
    $("#marca-edit").val(vehiculoEdit.marca);
    $("#modelo-edit").val(vehiculoEdit.modelo);
    $("#color-edit").val(vehiculoEdit.color);
    $("#anio-edit").val(vehiculoEdit.anio);
    $("#imagen-edit").attr('src', vehiculoEdit.imagen);
    vistaPreviaEdit();
    cargarSelect(tipoVehiculo, vehiculoEdit.id_tipo_vehiculo, $("#id-tipo-vehiculo-edit"));
    $("#modal-repartidor").modal('show');
});

$(document).on("click", ".guardar-repartidor", function() {
    const idRepartidor = $(this).attr("data-id");
    alertify.confirm("Asignar", "¿Está seguro de asignar este conductor al vehículo?",
    function() {
        updateVehiculo(idVehiculo, idRepartidor);
        $("#modal-repartidor").modal('hide');
    },
    function() {
        alertify.error('Cancelado');
    });
});

$(document).on("click", ".restore", function() {
    const idVehiculo = $(this).attr("data-restore");
    alertify.confirm("Restaurar", "Se restaurará el registro",
    function() {
        restoreVehiculo(idVehiculo);
    },
    function() {
        alertify.error('Cancelado');
    });
});


function cargarVehiculo() {
    const url = rutaApiRest + "vehiculo";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            vehiculo = response.data;
            cargarTablaVehiculo(vehiculo, false, table);

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
    cargarVehiculoEliminados();
}

function cargarTablaVehiculo(vehiculo, eliminados = false, table) {
    const vehiculoObject = [];
    vehiculo.forEach(element => {
        const object = {};
        object.id_vehiculo = element.id_vehiculo;
        object.placa = element.placa;
        object.marca = element.marca;
        object.modelo = element.modelo;
        object.color = element.color;
        object.anio = element.anio;
        object.tipo_vehiculo = element.tipo_vehiculo.nombre;
        object.imagen_td = `<img src="${ rutaLocal + element.imagen}" class="imagen">`;
        object.repartidor = element.repartidor == null ? "" : element.repartidor.persona.nombre + " " + element.repartidor.persona.paterno;
        const accionRestarurar = `<a data-restore="${element.id_vehiculo}" class="btn btn-info btn-sm restore" title="Resturar"><i class="bi bi-arrow-bar-up"></i></a>`;
        const accionIndex = `
                            <a data-id="${element.id_vehiculo}" class="btn btn-success btn-sm asignar" title="Asignar repartidor"><i class="fa fa-motorcycle"></i></a>
                            <a data-edit="${element.id_vehiculo}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                             <a data-delete="${element.id_vehiculo}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>`;
        
        object.acciones = eliminados == true ? accionRestarurar : accionIndex;
                        
        vehiculoObject.push(object);
    });

    table.bootstrapTable('load', vehiculoObject);
}

function saveVehiculo() {
    const formData = new FormData();

    formData.append('nombre', $("#nombre").val());
    formData.append('placa', $("#placa").val());
    formData.append('marca', $("#marca").val());
    formData.append('modelo', $("#modelo").val());
    formData.append('color', $("#color").val());
    formData.append('anio', $("#anio").val());
    formData.append('id_tipo_vehiculo', $("#id-tipo-vehiculo").val());

    const imagenInput = $("#imagen")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "vehiculo";
    showLoader();
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

                cargarVehiculo();
                limpiarInput();
            } else {
                alertify.alert(
                    "Error",
                    "¡Ocurrió un problema!"
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


function updateVehiculo(id, idRepartidor = 0) {
    const formData = new FormData();

    formData.append('nombre', $("#nombre-edit").val());
    formData.append('placa', $("#placa-edit").val());
    formData.append('marca', $("#marca-edit").val());
    formData.append('modelo', $("#modelo-edit").val());
    formData.append('color', $("#color-edit").val());
    formData.append('anio', $("#anio-edit").val());
    formData.append('id_repartidor', idRepartidor);
    formData.append('id_tipo_vehiculo', $("#id-tipo-vehiculo-edit").val());

    const imagenInput = $("#imagen-edit")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "vehiculo/" + id;
    showLoader();
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

                cargarVehiculo();
                limpiarInput();
            } else {
                alertify.alert(
                    "Error",
                    "¡Ocurrió un problema!"
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


function deleteVehiculo(id) {
    const url = rutaApiRest + "vehiculo/" + id;
    showLoader();
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

                cargarVehiculo();
            } else {
                alertify.alert(
                    "Error",
                    "¡Ocurrio un problema!"
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

function cargarVehiculoEliminados() {
    const url = rutaApiRest + "vehiculo-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            vehiculoEliminados = response.data;
            console.log(response);
            cargarTablaVehiculo(vehiculoEliminados, true, tableEliminados)

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function restoreVehiculo(id) {
    const url = rutaApiRest + "vehiculo-restaurar/" + id;
    showLoader();
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

                cargarVehiculo();
                cargarVehiculoEliminados();
            } else {
                alertify.alert(
                    "Correcto",
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

function limpiarInput() {
    $("#nombre").val("");
    $("#precio").val("");
    $("#descripcion").val("");
    $("#modal-nuevo-vehiculo").modal('hide');

    $("#nombre-edit").val("");
    $("#precio-edit").val("");
    $("#descripcion-edit").val("");
    $("#modal-edit-vehiculo").modal('hide');
}

function cargarTipoVehiculo() {
    const url = rutaApiRest + "tipo-vehiculo";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            tipoVehiculo = response.data;
            const idTipoVehiculo = 0;
            const selectGuardar = $("#id-tipo-vehiculo");
            const selectEditar = $("#id-tipo-vehiculo-edit");
            cargarSelect(tipoVehiculo, idTipoVehiculo, selectGuardar);
            cargarSelect(tipoVehiculo, idTipoVehiculo, selectEditar);
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}

function cargarRepartidores() {
    const url = rutaApiRest + "repartidor";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            repartidores = response.data;
            const select = $("#id-repartidor");
            cargarTablaRepartidor(repartidores, false, tableRepartidor);
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}


function cargarTablaRepartidor(repartidores, eliminados = false, table) {
    const repartidorPersona = [];
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
        object.acciones = `
                            <a data-id="${repartidor.id_repartidor}" class="btn btn-info btn-sm guardar-repartidor" title="Asignar"><i class="fa fa-save"></i></a>`;
                        
        repartidorPersona.push(object);
    });

    tableRepartidor.bootstrapTable('load', repartidorPersona);
}


function cargarSelect(array, id = 0, select) {
    select.select2({width: '100%', theme: "classic"});
    select.empty();
    array.forEach(element => {
        let selected = "";
        if (id == element.id_tipo_vehiculo) {
            selected = "selected";
        }
        select.append(
            `<option value="${element.id_tipo_vehiculo}" ${selected}>${element.nombre}</option>`
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
