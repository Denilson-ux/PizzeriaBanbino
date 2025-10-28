let catalogoMenu = [];
let tipoMenu = [];
let itemMenu = [];
let carrito = [];
// let users =[]; //corregir luego, (no debe estar en el front, hacer en el back)
let descuentoCliente = 0;

$(document).ready(() => {
    const carritomall = JSON.parse(localStorage.getItem('carritomall'));
    if (carritomall) {
        carrito = carritomall;
        cargarCardItemMenuCarrito(carritomall);
        montoTotal(carritomall);
    } else {
        $("#total-item").text(0);
    }


    cargarTipoMenu();
    cargarInformacionCliente();
})

$(document).on("click", "#iniciar-sesion", function () {
    iniciarSesion();
});

$(document).on("click", "#crear-cuenta", function () {
    window.location.href = "form";
    // const enlaceTemporal = document.createElement('a');
    // enlaceTemporal.href = rutaLocal + "form";
    // enlaceTemporal.click();
});

$(document).on("click", ".agregar-carrito", function () {
    const idItem = $(this).attr("data-carrito");

    const existeItem = carrito.find(element => element.id_item_menu == idItem);

    const object = verificarCantidad(idItem, 1); //cantidad 1, en caso que sea 0

    // if (object.verificar) {
        if (existeItem == undefined) {
            const item = itemMenu.find(element => element.id_item_menu == idItem);
            item.cantidad = item.cantidad == null ? 1 : item.cantidad + 1;
            item.sub_monto = item.precio * item.cantidad;
            carrito.push(item);
            cargarCardItemMenuCarrito(carrito);
            alertify.set('notifier', 'position', 'bottom-left');
            alertify.success("Agregado al carrito");
        } else {
            existeItem.cantidad += 1;
            existeItem.sub_monto = existeItem.precio * existeItem.cantidad;
            cargarCardItemMenuCarrito(carrito);
        }

        montoTotal(carrito);

        localStorage.setItem('carritomall', JSON.stringify(carrito));
    // } else {
    //     alerta("Producto agotado", "No hay mas de la cantidad actual en el restaurante", 1500);
    // }
    // alertify.success('Agregado al carrito');
});

$(document).on("click", ".btn-plus-agregado", function (e) {
    const idItemMenu = this.name;
    const itemAddCantidad = carrito.find(item => item.id_item_menu == idItemMenu);
    const inputCantidad = $("#" + idItemMenu);
    const spanSubmonto = $("#submonto-" + idItemMenu);
    const cantidadActualInput = inputCantidad.val();
    const cantidad = parseInt(cantidadActualInput) + 1;
    const objectVerificar = verificarCantidad(idItemMenu, cantidad);

    if (objectVerificar.verificar) {
        console.log("cantidad menor a stock");
        itemAddCantidad.cantidad = cantidad;
        itemAddCantidad.sub_monto = cantidad * itemAddCantidad.precio;

        inputCantidad.val(cantidad);
        spanSubmonto.text(itemAddCantidad.sub_monto);

        montoTotal(carrito);

        localStorage.setItem('carritomall', JSON.stringify(carrito));

    } else {
        alerta("Producto agotado", "No hay mas de la cantidad actual en el restaurante", 1500);
    }
});


$(document).on("click", ".btn-minus-agregado", function (e) {
    const idItemMenu = this.name;
    const itemAddCantidad = carrito.find(item => item.id_item_menu == idItemMenu);
    const inputCantidad = $("#" + idItemMenu);
    const spanSubmonto = $("#submonto-" + idItemMenu);
    const cantidadActualInput = inputCantidad.val();
    let cantidad = parseInt(cantidadActualInput);
    if (cantidad > 1) {
        cantidad -= 1;
        itemAddCantidad.cantidad = cantidad;
        itemAddCantidad.sub_monto = cantidad * itemAddCantidad.precio;

        inputCantidad.val(cantidad);
        spanSubmonto.text(itemAddCantidad.sub_monto);

        montoTotal(carrito);

        localStorage.setItem('carritomall', JSON.stringify(carrito));
    }
});

$(document).on("input", ".input-cantidad", function (e) {
    const id = this.id;
    const itemAddCantidad = carrito.find(item => item.id_item_menu == id);
    const inputCantidad = $("#" + id);
    const spanSubmonto = $("#submonto-" + id);
    const cantidad = parseInt(inputCantidad.val());
    const object = verificarCantidad(id, cantidad);

    if (cantidad <= 0) {
        alerta("Error", "No se permiten cantidades negativas", 1000);
        inputCantidad.val(1);
    } else {
        if (!object.verificar) {
            alerta("Item agotado", "No hay mas de la cantidad actual en el restaurante", 1500);
            inputCantidad.val(object.cantidad);
        } else {
            itemAddCantidad.cantidad = cantidad;
            itemAddCantidad.sub_monto = cantidad * itemAddCantidad.precio;
            spanSubmonto.text(itemAddCantidad.sub_monto);
        }
    }
});

//boton del card para eliminar item
$(document).on("click", ".eliminar-item-add", function (e) {
    const idItemMenu = this.name;
    const newItemTemp = carrito.filter(deleteItem => deleteItem.id_item_menu != idItemMenu);
    carrito = newItemTemp;

    cargarCardItemMenuCarrito(carrito);

    montoTotal(carrito);

    localStorage.setItem('carritoVenta', JSON.stringify(carrito));
});

$(document).on("click", "#proceder-pagar", () => {
    if (carrito.length > 0) {
        localStorage.setItem('carritomall', JSON.stringify(carrito));
        cargarDatosCliente();
    } else {
        alerta("Carrito vacío", "No tienes ningún producto en el carrito para realizar el pedido", 1500);
    }
});


function cargarCatalogoMenu() {
    // showLoader();
    const fecha = obtenerFechaActual();
    const url = rutaApiRest + "menu-fecha/" + fecha;
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            catalogoMenu = response.data;
            console.log(response);
            if (catalogoMenu.length > 0) {
                idMenu = catalogoMenu[0].id_menu;
                itemMenu = catalogoMenu[0].item_menus;
                cargarCardItemMenu(itemMenu);
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


function cargarTipoMenu() {
    // showLoader();
    const url = rutaApiRest + "tipo-menu";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            tipoMenu = response.data;
            cargarUlTipoMenu(tipoMenu);
            cargarCatalogoMenu();

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

function iniciarSesion() {
    showLoader();;
    const data = {
        email: $("#email-inicio").val(),
        password: $("#password-inicio").val(),
    };
    const dataEnviar = JSON.stringify(data);
    const url = rutaApiRest + "user-inicio-sesion";

    console.log(dataEnviar);
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: dataEnviar,
        success: function (response) {
            if (response.status == 200) {
                const data = response.data;
                const cliente = {
                    id_cliente: data.persona.id_persona,
                    nombre: data.persona.nombre,
                    paterno: data.persona.paterno,
                    correo: data.email,
                    descuento: 0,
                    persona: data.persona,
                    user: {
                        id: data.id,
                        id_persona: data.persona.id_persona,
                        id_rol: data.id_rol,
                        name: data.name,
                    },
                };

                console.log(cliente);
                localStorage.setItem('clientemall', JSON.stringify(cliente));
                alerta("Éxito", "Sesión iniciada correctamente", 1500);
                location.href = location.href;

            } else {
                alerta("Error", "Credenciales incorrectas", 1500);
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

function cargarUlTipoMenu(tipMenu) {
    const ulTipoMenu = $("#ul-tipo-menu");
    ulTipoMenu.html("");
    ulTipoMenu.append(`<li class="active" data-filter="*">All</li>`);
    tipMenu.forEach(element => {
        ulTipoMenu.append(`<li data-filter="${element.nombre}">${element.nombre}</li>`);
    });

    // Agregar evento de clic para manejar el filtrado
    ulTipoMenu.find('li').on('click', function () {
        const filtro = $(this).data('filter');
        cargarCardItemMenuFiltrado(filtro);
    });
}

function cargarCardItemMenu(itemMenu) {
    const carContenedor = $("#contenido-item-menu");
    carContenedor.html("");
    itemMenu.forEach(item => {
        item.tipo_menu = tipoMenu.find(tipo => tipo.id_tipo_menu == item.id_tipo_menu);
        const cuerpo = `
            <div class="tarjeta-menu col-sm-6 col-lg-4" data-tipo-menu="${item.tipo_menu.nombre}">
                <div class="box">
                    <div>
                        <div class="img-box img-content">
                            <img src="${rutaLocal + item.imagen}" alt="${item.nombre}">
                        </div>
                        <div class="detail-box">
                            <h5>${item.nombre}</h5>
                            <p>${item.descripcion}</p>
                            <div class="options">
                                <h6>$${item.precio}</h6>
                                <button data-carrito="${item.id_item_menu}" class="btn btn-warning btn-sm agregar-carrito" href="#" style="color: white">
                                    <i class="fa fa-shopping-cart" aria-hidden="true"> Agregar al carrito</i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        carContenedor.append(cuerpo);
    });
}

function cargarCardItemMenuCarrito(itemcarrito) {
    const carContenedor = $("#content-modal-carrito");
    carContenedor.html("");
    itemcarrito.forEach(item => {
        const cuerpo = `
        <div class="p-1">
            <div class="card card-sm menu-card buscar-agregado" style="width: 10rem;">
                <img src="${rutaLocal + item.imagen}" alt="${item.nombre}">
                <div class="menu-card-content pb-0">
                    <h3>${item.nombre}</h3>
                    <p>Precio: <span class="menu-card-price">Bs ${item.precio}</span></p>
                    <p>Submonto: <span class="menu-card-price" id="submonto-${item.id_item_menu}">Bs ${item.sub_monto}</span></p>
                    <div class="input-group mb-3">
                        <input type="number" class="form-control form-control-sm input-cantidad" value="${item.cantidad}" id="${item.id_item_menu}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-dark btn-sm mx-1  btn-plus-agregado" name="${item.id_item_menu}">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <div class="input-group-append">
                            <button type="button" class="btn btn-warning btn-sm btn-minus-agregado" name="${item.id_item_menu}">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center my-1">
                            <button class="btn btn-danger btn-sm mx-1 eliminar-item-add" name="${item.id_item_menu}">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        carContenedor.append(cuerpo);
    });

    $("#total-item").text(itemcarrito.length);
}

function cargarCardItemMenuFiltrado(filtro) {
    const carContenedor = $("#contenido-item-menu");

    // Mostrar todas las tarjetas
    carContenedor.find('.tarjeta-menu').slideDown(1);

    // Ocultar las tarjetas que no corresponden a la categoría seleccionada
    if (filtro !== '*') {
        carContenedor.find(`.tarjeta-menu:not([data-tipo-menu="${filtro}"])`).slideUp(1);
    }

    // Agregar la clase 'active' al tipo de menú seleccionado
    $('#ul-tipo-menu li').removeClass('active');
    $(`#ul-tipo-menu li[data-filter="${filtro}"]`).addClass('active');
}

function cargarDatosCliente() {
    const clientemall = JSON.parse(localStorage.getItem('clientemall'));
    if (clientemall) {
        $("#nombre-usuario").text(clientemall.user.name);
        window.location.href = 'confirmar';
    } else {
        alertify.confirm("Registrarse", "Necesitas registrarte para proceder a la compra",
            function () {
                window.location.href = 'form';
            },
            function () {
                alertify.error('Cancelado');
            });
    }
}

function cargarInformacionCliente() {
    const clientemall = JSON.parse(localStorage.getItem('clientemall'));
    if (clientemall) {
        $("#nombre-usuario").text(clientemall.user.name);
        $("#nombre").val(clientemall.nombre);
        $("#email").val(clientemall.correo);
        $("#telefono").val(clientemall.telefono);
    }

    setTimeout(() => {
        $("#registrarme-nav").removeClass("d-none");
        $("#nav-carrito-search").removeClass("d-none");
        $("#dropdwn-user").removeClass("d-none");
    }, 1);
}


function iniciarSesión() {

}

function verificarCantidad(idItem, cantidad) {
    const item = carrito.find(element => element.id_item_menu == idItem);
    const object = {};
    if (item) {
        object.cantidad = item.pivot.cantidad;
        if (cantidad > object.cantidad) {
            object.verificar = false;
        } else {
            object.verificar = true;
        }
    }
    return object;
}

function montoTotal(array) {
    let monto = 0;
    const montoInput = $("#monto");
    const descuentoInput = $("#monto-descuento");
    const descuento = descuentoCliente;
    array.forEach(element => {
        monto += parseFloat(element.sub_monto);
    });

    const montoDescuento = calcularMontoConDescuento(monto, descuento).toFixed(2);

    descuentoInput.val(montoDescuento);
    montoInput.text(monto);

    return {
        monto: monto,
        monto_descuento: montoDescuento
    };
}

function calcularMontoConDescuento(montoTotal, porcentajeDescuento) {

    const descuento = montoTotal * (porcentajeDescuento / 100);
    const montoConDescuento = montoTotal - descuento;

    return montoConDescuento;
}


function obtenerFechaActual() {
    var fechaActual = new Date();
    var año = fechaActual.getFullYear();
    var mes = ('0' + (fechaActual.getMonth() + 1)).slice(-2);
    var dia = ('0' + fechaActual.getDate()).slice(-2);

    var fechaFormateada = año + '-' + mes + '-' + dia;
    return fechaFormateada;
}


function alerta(titulo, mensaje, duracion) {
    const alerta = alertify.alert(titulo, mensaje);
    setTimeout(function () {
        alerta.close();
    }, duracion);
}


function previewImage(event) {
    var input = event.target;

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            document.getElementById('preview').src = e.target.result;
        }

        reader.readAsDataURL(input.files[0]);
    }
}