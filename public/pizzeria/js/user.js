let user = [];
let rol = [];
let userEliminados = [];
let table = $("#tabla-user");
let tableEliminados = $("#tabla-user-eliminados");
let empleados = [];
let repartidores = [];
let clientes = [];

$(document).ready(() => {
    cargarUser();
    cargarRol();
    cargarEmpleado();
    cargarRepartidor();
    cargarCliente();
    
    console.log('🔧 Sistema de usuarios iniciado');
    
    // Eventos para cargar personas por tipo - MODAL NUEVO
    $('#tipo_persona').on('change', function() {
        var tipoSeleccionado = $(this).val();
        console.log('✅ Tipo persona seleccionado (NUEVO):', tipoSeleccionado);
        cargarPersonasPorTipo(tipoSeleccionado, '#id_persona', '[NUEVO] ');
    });

    // Eventos para cargar personas por tipo - MODAL EDITAR
    $('#tipo_persona_edit').on('change', function() {
        var tipoSeleccionado = $(this).val();
        console.log('✅ Tipo persona seleccionado (EDIT):', tipoSeleccionado);
        cargarPersonasPorTipo(tipoSeleccionado, '#id_persona_edit', '[EDIT] ');
    });
    
    // Al abrir modal NUEVO: limpiar SIEMPRE todos los campos
    $('#modal-nuevo-user').on('show.bs.modal', function(){
    limpiarNuevo();
    });

    // CRÍTICO: Limpiar search box de Bootstrap Table al cargar página
    $(document).ready(() => {
    // ... código existente ...
    
    // Forzar limpieza del search box de Bootstrap Table
    setTimeout(() => {
        $('.bootstrap-table .fixed-table-toolbar .search input').val('').attr('placeholder', 'Search');
        console.log('🧹 Search box limpiado');
    }, 500);
    });

    // Función para limpiar TODOS los inputs incluyendo search de tabla
function limpiarTodosSistemasAutofill() {
    // Limpiar search box de bootstrap table
    $('.bootstrap-table .fixed-table-toolbar .search input').val('');
    $('.bootstrap-table .fixed-table-toolbar .search input').attr('placeholder', 'Search');
    
    // Limpiar cualquier input con email guardado
    $('input[type="email"]').val('');
    $('input[type="search"]').val('');
    
    console.log('🧹 Limpieza completa de autofill ejecutada');
}


});

$("#btn-nuevo-user").click(() => {
    console.log('🔄 Abriendo modal nuevo usuario');
    $("#modal-nuevo-user").modal('show');
});

$("#guardar-user").click(() => {
    console.log('💾 Iniciando validación para guardar usuario...');
    
    if (validar($("#name")) && 
        validar($("#email")) && 
        validar($("#password")) && 
        validar($("#password-repite")) &&
        verifyEmail() &&
        verifyPassword() &&
        validarSelect($("#tipo_persona")) && 
        validarSelect($("#id_persona"))) 
        {
        console.log('✅ Todas las validaciones pasaron, guardando usuario...');
        saveUser();
    } else {
        if (!validarSelect($("#tipo_persona")) || !validarSelect($("#id_persona"))) {
            console.log('❌ Error: Tipo o persona no seleccionados');
            const alerta = alertify.alert("Advertencia", "Debe seleccionar un tipo de persona y una persona específica");
            setTimeout(function(){
                alerta.close();
            }, 2500);
        }
    }
});

$("#actualizar-user").click(() => {
    const id = $("#actualizar-user").attr('name');
    console.log('🔄 Actualizando usuario ID:', id);
    
    if (validar($("#name-edit")) && 
        validar($("#email-edit")) && 
        verifyEmailEdit(id) &&
        validarSelect($("#tipo_persona_edit")) && 
        validarSelect($("#id_persona_edit"))) 
        {
        updateUser(id);
    }  
});

$(document).on("click", ".edit", function() {
    const id = $(this).attr("data-edit");
    console.log('✏️ Editando usuario ID:', id);

    const userEdit = user.find((element) => {
        return element.id == id;
    });
    
    if (!userEdit) {
        console.error('❌ Usuario no encontrado para editar');
        return;
    }
    
    // Llenar datos básicos
    $("#name-edit").val(userEdit.name);
    $("#email-edit").val(userEdit.email);
    // NO llenar contraseñas - dejarlas vacías para editar opcional
    $("#password-edit").val("");
    $("#password-repite-edit").val("");
    $("#imagen-edit").attr('src', userEdit.profile_photo_path);
    $("#actualizar-user").attr("name", id);
    
    // Cargar tipo_persona y id_persona si existen
    if (userEdit.tipo_persona) {
        console.log('📋 Precargando tipo_persona:', userEdit.tipo_persona);
        $("#tipo_persona_edit").val(userEdit.tipo_persona).trigger('change');
        
        // Esperar a que se carguen las personas y luego seleccionar
        setTimeout(() => {
            console.log('👤 Precargando id_persona:', userEdit.id_persona);
            $("#id_persona_edit").val(userEdit.id_persona).trigger('change');
        }, 800);
    } else {
        console.log('⚠️ Usuario sin tipo_persona, reseteando selects');
        // Si no tiene tipo_persona, resetear selects
        $("#tipo_persona_edit").val("").trigger('change');
        $("#id_persona_edit").val("").trigger('change');
    }
    
    $("#modal-edit-user").modal('show');
    vistaPreviaEdit();
});

$(document).on("click", ".delete", function() {
    const id = $(this).attr("data-delete");
    alertify.confirm("¿Está seguro de eliminar este registro?", "Se borrará el registro",
    function() {
        deleteUser(id);
    },
    function() {
        alertify.error('Cancelado');
    });
});

$(document).on("click", ".restore", function() {
    const id = $(this).attr("data-restore");
    alertify.confirm("Restaurar", "Se restaurará el registro",
    function() {
        restoreUser(id);
    },
    function() {
        alertify.error('Cancelado');
    });
});

$("#email").on('input', function () {
    verifyEmail();
});

// Función para cargar personas según tipo seleccionado
function cargarPersonasPorTipo(tipoPersona, selectPersona, debugPrefix) {
    debugPrefix = debugPrefix || '';
    console.log(debugPrefix + '🔍 Cargando personas para tipo:', tipoPersona);
    
    if (!tipoPersona) {
        $(selectPersona).prop('disabled', true).html('<option value="">Primero selecciona el tipo...</option>');
        return;
    }

    $(selectPersona).prop('disabled', false).html('<option value="">Cargando...</option>');
    
    var url = '/admin/personas?tipo=' + tipoPersona;
    console.log(debugPrefix + '🌐 Realizando petición a:', url);
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log(debugPrefix + '✅ Respuesta exitosa:', response);
            
            if (!response || !Array.isArray(response)) {
                console.error(debugPrefix + '❌ Respuesta no es un array válido:', response);
                $(selectPersona).html('<option value="">Error: respuesta inválida</option>');
                return;
            }
            
            var options = '<option value="">Selecciona una persona...</option>';
            for (var i = 0; i < response.length; i++) {
                var persona = response[i];
                options += '<option value="' + persona.id + '">' + persona.nombre_completo + '</option>';
            }
            
            $(selectPersona).html(options);
            console.log(debugPrefix + '📝 Select actualizado con', response.length, 'opciones');
        },
        error: function(xhr, status, error) {
            console.error(debugPrefix + '❌ Error en petición AJAX:', {
                xhr: xhr,
                status: status,
                error: error,
                responseText: xhr.responseText
            });
            $(selectPersona).html('<option value="">Error al cargar personas</option>');
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
            console.log('📊 Usuarios cargados:', response);
            user = response.data;
            cargarTablaUser(user, false, table);
            hideLoader();

            setTimeout(limpiarTodosSistemasAutofill, 100);
        },
        error: function (data, textStatus, jqXHR, error) {
            console.error('❌ Error cargando usuarios:', data, textStatus, error);
            hideLoader();
        }
    });
    cargarUserEliminados();
}

function cargarTablaUser(user, eliminados = false, table) {
    const userObject = [];
    user.forEach(element => {
        const object = {};
        object.id = element.id;
        object.name = element.name;
        object.email = element.email;
        object.rol = element.rol !== null ? element.rol.nombre : "";
        object.persona_info = element.persona_info || 'Sin persona asignada';
        object.imagen_td = `<img src="${ rutaLocal + element.profile_photo_path}" class="imagen">`;

        const accionRestarurar = `<a data-restore="${element.id}" class="btn btn-info btn-sm restore" title="Restaurar"><i class="bi bi-arrow-bar-up"></i></a>`;
        const accionIndex = `<a data-edit="${element.id}" class="btn btn-warning btn-sm edit" title="Editar"><i class="fa fa-edit"></i></a>
                             <a data-delete="${element.id}" class="btn btn-danger btn-sm delete" title="Borrar"><i class="fa fa-trash"></i></a>`;
        
        object.acciones = eliminados == true ? accionRestarurar : accionIndex;
                        
        userObject.push(object);
    });

    table.bootstrapTable('load', userObject);
}

function saveUser() {
    console.log('💾 === INICIANDO SAVEUSER ===');
    
    // CRÍTICO: Verificación de existencia de elementos
    const $tipoPersona = $("#tipo_persona");
    const $idPersona = $("#id_persona");
    
    console.log('🔍 Verificando elementos:');
    console.log('- Elemento tipo_persona existe:', $tipoPersona.length);
    console.log('- Elemento id_persona existe:', $idPersona.length);
    
    if ($tipoPersona.length === 0 || $idPersona.length === 0) {
        console.error('❌ ERROR CRÍTICO: Elementos no encontrados en el DOM');
        alertify.error('Error: Elementos del formulario no encontrados. Recarga la página.');
        return;
    }
    
    // CRÍTICO: Obtener valores con validación
    const tipoPersona = $tipoPersona.val();
    const idPersona = $idPersona.val();
    
    console.log('📋 Valores capturados:');
    console.log('- Tipo persona:', tipoPersona, '(tipo:', typeof tipoPersona, ')');
    console.log('- ID persona:', idPersona, '(tipo:', typeof idPersona, ')');
    
    // CRÍTICO: Validación estricta
    if (!tipoPersona || tipoPersona === '' || tipoPersona === null || tipoPersona === undefined) {
        console.error('❌ ERROR: Tipo persona vacío o inválido');
        alertify.error("Error: Debe seleccionar un tipo de persona");
        return;
    }
    
    if (!idPersona || idPersona === '' || idPersona === null || idPersona === undefined) {
        console.error('❌ ERROR: ID persona vacío o inválido');
        alertify.error("Error: Debe seleccionar una persona específica");
        return;
    }
    
    // Crear FormData
    const formData = new FormData();
    formData.append('name', $("#name").val());
    formData.append('email', $("#email").val());
    formData.append('password', $("#password").val());
    formData.append('tipo_persona', tipoPersona);
    formData.append('id_persona', idPersona);
    
    console.log('📦 FormData preparado:');
    console.log('- name:', $("#name").val());
    console.log('- email:', $("#email").val());
    console.log('- password: [OCULTO]');
    console.log('- tipo_persona:', tipoPersona);
    console.log('- id_persona:', idPersona);

    const imagenInput = $("#imagen")[0];
    if (imagenInput.files.length > 0) {
        formData.append('profile_photo_path', imagenInput.files[0]);
        console.log('📷 Imagen agregada al FormData');
    }

    const url = rutaApiRest + "user";
    console.log('🌐 Enviando a URL:', url);
    
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false, 
        processData: false,
        success: function (response) {
            console.log('✅ Respuesta del servidor:', response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se insertó correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                cargarUser();
                limpiarInput();
            } else {
                console.error('❌ Error del servidor:', response);
                alertify.alert(
                    "Error",
                    "¡Ocurrió un problema: " + (response.message || "Error desconocido")
                );
            }
        },
        error: function (xhr, textStatus, error) {
            console.error('❌ Error AJAX:', {
                xhr: xhr,
                textStatus: textStatus,
                error: error,
                responseText: xhr.responseText
            });
            
            let errorMsg = 'Error desconocido';
            try {
                const responseJson = JSON.parse(xhr.responseText);
                if (responseJson.message) {
                    errorMsg = responseJson.message;
                } else if (responseJson.errors) {
                    errorMsg = Object.values(responseJson.errors).flat().join(', ');
                }
            } catch (e) {
                errorMsg = xhr.responseText || 'Error de conexión';
            }
            
            alertify.error("Error al guardar usuario: " + errorMsg);
        }
    });
}

function updateUser(id) {
    console.log('🔄 === INICIANDO UPDATEUSER ===');
    
    const formData = new FormData();
    const tipoPersona = $("#tipo_persona_edit").val();
    const idPersona = $("#id_persona_edit").val();
    const password = $("#password-edit").val();

    console.log('📋 UPDATE - Valores capturados:');
    console.log('- Tipo persona:', tipoPersona);
    console.log('- ID persona:', idPersona);
    console.log('- Password length:', password ? password.length : 'empty');

    formData.append('name', $("#name-edit").val());
    formData.append('email', $("#email-edit").val());
    
    // Solo enviar contraseña si se escribió algo
    if (password && password.length > 0) {
        formData.append('password', password);
    }
    
    formData.append('tipo_persona', tipoPersona);
    formData.append('id_persona', idPersona);

    const imagenInput = $("#imagen-edit")[0];
    if (imagenInput.files.length > 0) {
        formData.append('profile_photo_path', imagenInput.files[0]);
    }

    const url = rutaApiRest + "user/" + id;
    console.log('🌐 Actualizando en URL:', url);
    
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            console.log('✅ Usuario actualizado:', response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se actualizó correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                cargarUser();
                limpiarInput();
            } else {
                console.error('❌ Error del servidor:', response);
                alertify.alert(
                    "Error",
                    "¡Ocurrió un problema: " + (response.message || "Error desconocido")
                );
            }
        },
        error: function (xhr, textStatus, error) {
            console.error('❌ Error al actualizar usuario:', xhr, textStatus, error);
            
            let errorMsg = 'Error desconocido';
            try {
                const responseJson = JSON.parse(xhr.responseText);
                errorMsg = responseJson.message || Object.values(responseJson.errors || {}).flat().join(', ');
            } catch (e) {
                errorMsg = xhr.responseText || 'Error de conexión';
            }
            
            alertify.error("Error al actualizar usuario: " + errorMsg);
        }
    });
}

function deleteUser(id) {
    const url = rutaApiRest + "user/" + id;
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

                cargarUser();
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

function cargarUserEliminados() {
    const url = rutaApiRest + "user-eliminados";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            userEliminados = response.data;
            console.log(response);
            cargarTablaUser(userEliminados, true, tableEliminados)

        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function restoreUser(id) {
    const url = rutaApiRest + "user-restaurar/" + id;
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

                cargarUser();
                cargarUserEliminados();
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

// FUNCIONES DE LIMPIEZA MEJORADAS
function limpiarNuevo(){
    console.log('🧽 Limpiando campos del modal NUEVO...');
    $("#name").val("");
    $("#email").val("");
    $("#password").val("");
    $("#password-repite").val("");
    $("#tipo_persona").val("").trigger('change');
    $("#id_persona").html('<option value="">Primero selecciona el tipo...</option>').prop('disabled', true);
    $("#imagen").val("");
    $("#vista-previa").hide().attr('src','');
    
    // Limpiar cualquier mensaje de error
    $("#email-advertencia").addClass("d-none");
}

function limpiarInput() {
    console.log('🧽 Limpiando ambos modales...');
    // Limpieza para modal NUEVO
    limpiarNuevo();

    // Limpieza para modal EDITAR
    $("#name-edit").val("");
    $("#email-edit").val("");
    $("#password-edit").val("");
    $("#password-repite-edit").val("");
    $("#tipo_persona_edit").val("").trigger('change');
    $("#id_persona_edit").val("").trigger('change');
    $("#imagen-edit").val("");
    $("#vista-previa-edit").hide().attr('src','');
    
    // Cerrar modales
    $("#modal-nuevo-user").modal('hide');
    $("#modal-edit-user").modal('hide');
    
    // Limpiar mensajes de error
    $("#email-advertencia-edit").addClass("d-none");
}

// FUNCIÓN HELPER PARA VALIDACIÓN DE SELECTS
function validarSelect($elemento) {
    const valor = $elemento.val();
    const esValido = valor && valor !== '' && valor !== null && valor !== undefined;
    console.log('🔍 Validando select:', $elemento.attr('id'), '- Valor:', valor, '- Válido:', esValido);
    return esValido;
}

// Funciones heredadas mantenidas para compatibilidad
function cargarRol() {
    const url = rutaApiRest + "rol";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            rol = response.data;
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function cargarEmpleado() {
    const url = rutaApiRest + "empleado";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            empleados = response.data;
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function cargarRepartidor() {
    const url = rutaApiRest + "repartidor";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            repartidores = response.data;
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
    });
}

function cargarCliente() {
    const url = rutaApiRest + "cliente";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            clientes = response.data;
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }
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
        console.warn('Error en vista previa edit:', error);
    }
}

function verifyEmail() {
    const mailTemp = $("#email").val();
    const userMail = user.find(element => element.email == mailTemp);
    let result = true;
    if (userMail != undefined) {
        setTimeout(() => {
            $("#email-advertencia").removeClass("d-none");
            $("#email-advertencia").text("Correo no disponible");
            result = false;
        }, 1000);
    } else {
        $("#email-advertencia").addClass("d-none");
    }
    return result;
}

function verifyPassword() {
    const password = document.getElementById('password').value;
    const repeatPassword = document.getElementById('password-repite').value;
    let result = true;

    if (password.length < 8) {
        alert('La contraseña debe tener al menos 8 caracteres.');
        return false;
    }

    if (password !== repeatPassword) {
        alert('Las contraseñas no coinciden.');
        result = false;
    }
    
    return result;
}

function verifyEmailEdit(id) {
    const mailTemp = $("#email-edit").val();
    const userMail = user.find(element => element.email == mailTemp);
    let result = true;
    if (userMail != undefined) {
        if (!(userMail.email == mailTemp && userMail.id == id)) {
            setTimeout(() => {
                $("#email-advertencia-edit").removeClass("d-none");
                $("#email-advertencia-edit").text("Correo no disponible");
                result = false;
            }, 1000);
        } else {
            $("#email-advertencia-edit").addClass("d-none");
        }
    } else {
        $("#email-advertencia-edit").addClass("d-none");
    }
    return result;
}

// Mostrar contraseña - versión corregida sin classList
document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });
    }

    const togglePasswordRepite = document.getElementById('toggle-password-repite');
    const passwordRepiteInput = document.getElementById('password-repite');

    if (togglePasswordRepite && passwordRepiteInput) {
        togglePasswordRepite.addEventListener('click', function () {
            const type = passwordRepiteInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordRepiteInput.setAttribute('type', type);
        });
    }

    const togglePasswordEdit = document.getElementById('toggle-password-edit');
    const passwordInputEdit = document.getElementById('password-edit');

    if (togglePasswordEdit && passwordInputEdit) {
        togglePasswordEdit.addEventListener('click', function () {
            const type = passwordInputEdit.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInputEdit.setAttribute('type', type);
        });
    }

    const togglePasswordRepiteEdit = document.getElementById('toggle-password-repite-edit');
    const passwordRepiteInputEdit = document.getElementById('password-repite-edit');

    if (togglePasswordRepiteEdit && passwordRepiteInputEdit) {
        togglePasswordRepiteEdit.addEventListener('click', function () {
            const type = passwordRepiteInputEdit.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordRepiteInputEdit.setAttribute('type', type);
        });
    }
});