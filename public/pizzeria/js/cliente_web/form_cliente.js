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

                saveUser(cliente.persona.id_persona);
                
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

function saveUser(idCliente) {
    const formData = new FormData();

    formData.append('name', $("#usuario").val());
    formData.append('email', $("#correo").val());
    formData.append('password', $("#password").val());
    formData.append('id_rol', 4);
    formData.append('id_persona', idCliente);

    const url = rutaApiRest + "user";
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false, 
        processData: false,
        success: function (response) {
            console.log(response);
            cliente.user = response.data; //response.data contiene el objeto user creado

            localStorage.setItem('clientemall', JSON.stringify(cliente));
            window.location.href = rutaLocal;
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
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