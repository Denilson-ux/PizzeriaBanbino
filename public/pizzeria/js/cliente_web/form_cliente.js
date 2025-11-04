let cliente = {};
let users = []; //corregir esta parte
$(document).ready(function () {
    cargarUser();
    $("#registrarme-nav").addClass("d-none");
    $("#nav-carrito-search").addClass("d-none");
    $("#dropdwn-user").addClass("d-none");
});

$("#btn-registrar").click(() => {
    if (validar($("#nombre")) && 
        validar($("#usuario")) && 
        validar($("#paterno")) && 
        validar($("#telefono")) && 
        validar($("#correo")) &&
        validar($("#password")) && 
        validar($("#password-repite")) &&
        verifyEmail() &&
        verifyPassword()) {
        saveCliente();
    } 
});

$("#correo").on('input', function () {
    verifyEmail();
});

function saveCliente() {
    const data = {};
    data.nombre = $("#nombre").val();
    data.paterno = $("#paterno").val();
    // data.materno = $("#materno").val();
    data.telefono = $("#telefono").val();
    data.correo = $("#correo").val();
    data.descuento = 0;
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
                const alerta = alertify.alert("Correcto", "¡Súper, te registraste correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                
                cliente = data;
                cliente.persona = response.data; //response.data = persona
                cliente.id_cliente = cliente.persona.id_persona

                saveUserClienteWeb(cliente.persona.id_persona);
                
                console.log(cliente);
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

function verifyEmail() {
    const mailTemp = $("#correo").val();
    const userMail = users.find(element => element.email == mailTemp);
    let result = true;
    if (userMail != undefined) {
        setTimeout(() => {
            $("#correo-advertencia").removeClass("d-none");
            $("#correo-advertencia").text("Correo no disponible");
            result = false;
        }, 1000);
    } else {
        $("#correo-advertencia").addClass("d-none");
    }
    return result;
}

function verifyPassword() {
    const password = document.getElementById('password').value;
    const repeatPassword = document.getElementById('password-repite').value;
    const advertencias = document.querySelectorAll('.password-advertencia');
    let result = true;

    if (password !== repeatPassword) {
        advertencias[0].classList.remove('d-none');
        advertencias[1].classList.remove('d-none');
        result = false;
    } else {
        advertencias[0].classList.add('d-none');
        advertencias[1].classList.add('d-none');
    }
    return result;
}

/**
 * Función especial para crear usuarios de clientes web
 * Usa el endpoint que asigna automáticamente el rol "Cliente" por nombre
 */
function saveUserClienteWeb(idCliente) {
    const userData = {
        name: $("#usuario").val(),
        email: $("#correo").val(),
        password: $("#password").val(),
        id_persona: idCliente
    };

    const url = rutaApiRest + "user-cliente-web";
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: userData,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                cliente.user = response.data; //response.data contiene el objeto user creado con rol "Cliente" asignado

                // Mostrar mensaje de éxito
                alertify.success('¡Usuario creado exitosamente con rol de Cliente!');
                
                localStorage.setItem('clientemall', JSON.stringify(cliente));
                window.location.href = rutaLocal;
            } else {
                alertify.alert(
                    "Error",
                    "Error al crear el usuario: " + (response.message || "Problema desconocido")
                );
            }
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log("Error creando usuario:", data);
            let errorMessage = "Error al crear el usuario.";
            
            if (data.responseJSON && data.responseJSON.message) {
                errorMessage = data.responseJSON.message;
            } else if (data.responseText) {
                try {
                    const errorData = JSON.parse(data.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Si no se puede parsear, usar mensaje por defecto
                }
            }
            
            alertify.alert("Error", errorMessage);
        }
    });
}

/**
 * Función legacy - mantenida para compatibilidad pero ya no se usa
 * La nueva función saveUserClienteWeb() la reemplaza
 */
function saveUser(idCliente) {
    console.warn("saveUser() is deprecated. Use saveUserClienteWeb() instead.");
    saveUserClienteWeb(idCliente);
}


function cargarUser() {
    const url = rutaApiRest + "user";
    showLoader();
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            console.log(response);
            users = response.data;
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