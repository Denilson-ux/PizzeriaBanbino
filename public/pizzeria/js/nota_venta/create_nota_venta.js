let notaVentas = [];
let itemMenus = [];
let itemsCarrito = [];
let catalogoMenu = [];
let userAutenticado = user; //user es un objeto que lo obtenemos de create.blade.php
// let clientes = [];
let idMenu = 0;
let descuentoCliente = 0;

$(document).ready( () => {
    const carritoStorage = JSON.parse(localStorage.getItem('carritoVenta'));
    if (carritoStorage) {
        if (carritoStorage.length > 0) {
            cargarItemMenuAgregado(carritoStorage);
            itemsCarrito = carritoStorage;

           
            montoTotal(itemsCarrito);
        }
    }
    cargarCatalogoMenu();
    cargarClientesAsync();
    $("#fecha").val(obtenerFechaActual());
});

$("#guardar-nota-venta").click(() => {
    if (validar($("#monto")) &&
        itemsCarrito.length > 0) {
        saveNotaVenta();
    } 
});

$("#btn-search-cliente").click(() => {
   $("#modal-lista-cliente").modal('show');
});

//Buscadores para los items
$('#buscar').on('keyup', function () {
    realizarBusqueda('#buscar', '.buscar-disponible');
});

$('#buscar-add').on('keyup', function () {
    realizarBusqueda('#buscar-add', '.buscar-agregado');
});


//boton para agregar del card para agregar item
$(document).on("click", ".agregar-disponible", function(e) {
    const idItemMenu = this.name;
    const itemAdd = itemMenus.find(item => item.id_item_menu == idItemMenu);
    const objectVerificar = verificarCantidad(idItemMenu, 1);
    if (!itemAdd.add) {

        if (objectVerificar.verificar) {
            itemAdd.add = true;
            itemAdd.sub_monto = itemAdd.precio;
            itemAdd.id_menu = idMenu;

            itemsCarrito.push(itemAdd);
            cargarItemMenuAgregado(itemsCarrito);
            
            montoTotal(itemsCarrito);

            localStorage.setItem('carritoVenta', JSON.stringify(itemsCarrito));
        } else {
            alerta("Item agotado", "Item agotado :c, no hay más del plato", 1500);
        }
    }
});

//boton del card para eliminar item
$(document).on("click", ".eliminar-item-add", function(e) {
    const idItemMenu = this.name;
    const itemADelete = itemMenus.find(item => item.id_item_menu == idItemMenu);
    itemADelete.add = false;
    const newItemTemp = itemsCarrito.filter(deleteItem => deleteItem.id_item_menu != idItemMenu);
    itemsCarrito = newItemTemp;

    cargarItemMenuAgregado(itemsCarrito);

    montoTotal(itemsCarrito);

    localStorage.setItem('carritoVenta', JSON.stringify(itemsCarrito));
});

//boton del card para sumar cantidad
$(document).on("click", ".btn-plus-agregado", function(e) {
    const idItemMenu = this.name;
    const itemAddCantidad = itemsCarrito.find(item => item.id_item_menu == idItemMenu);
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

        montoTotal(itemsCarrito);
        
        localStorage.setItem('carritoVenta', JSON.stringify(itemsCarrito));
    } else {
        alerta("Item agotado", "No hay mas de la cantidad actual en el restaurante", 1500);
    }
});

//boton del card para restar cantidad
$(document).on("click", ".btn-minus-agregado", function(e) {
    const idItemMenu = this.name;
    const itemAddCantidad = itemsCarrito.find(item => item.id_item_menu == idItemMenu);
    const inputCantidad = $("#" + idItemMenu);
    const spanSubmonto = $("#submonto-" + idItemMenu);
    const cantidadActualInput = inputCantidad.val();
    let cantidad = parseInt(cantidadActualInput);
    if (cantidad > 1){
        cantidad -= 1;
        itemAddCantidad.cantidad = cantidad;
        itemAddCantidad.sub_monto = cantidad * itemAddCantidad.precio;

        inputCantidad.val(cantidad);
        spanSubmonto.text(itemAddCantidad.sub_monto);

        montoTotal(itemsCarrito);

        localStorage.setItem('carritoVenta', JSON.stringify(itemsCarrito));
    }
});

//listener para el input cantidad controlar cantidades negativas
$(document).on("input", ".input-cantidad", function(e) {
    const id = this.id;
    const itemAddCantidad = itemsCarrito.find(item => item.id_item_menu == id);
    const inputCantidad = $("#" + id);
    const spanSubmonto = $("#submonto-" + id);
    const cantidad = parseInt(inputCantidad.val());
    const object = verificarCantidad(id, cantidad);
    
    if ( cantidad <= 0) {
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

//seleccionar cliente
$(document).on("click", ".check", function() {
    const idCliente = $(this).attr("data-check");
    const cliente  = clientes.find(cliente => cliente.id_cliente == idCliente);   
    $("#descuento-venta").val(cliente.descuento); 
    $("#modal-lista-cliente").modal('hide');
    cargarSelectClientes(clientes, idCliente);
    
});

$('#id-cliente').on('select2:select', function (e) {
    const idCliente = $("#id-cliente").val();
    const cliente  = clientes.find(cliente => cliente.id_cliente == idCliente); 
    descuentoCliente = cliente.descuento;  
    montoTotal(itemsCarrito);
    $("#descuento-venta").val(cliente.descuento); 

});

function cargarCardItemsMenu(items) {
    const contenedorCards = $("#content-card-disponible");
    contenedorCards.html("");
    items.forEach(item => {
        item.add = false;
        item.cantidad = 1;
        const cardCuerpo = `
        <div class="col-md-6 col-sm-5 col-lg-4 col-xl-4 mb-2">
            <div class="card card-sm menu-card buscar-disponible" style="width: 10rem;">
                <img src="${rutaLocal + item.imagen}" alt="${item.nombre}">
                    <div class="menu-card-content">
                        <h3>${item.nombre}</h3>
                        <p>${item.descripcion}</p>
                        <p>Precio: <span class="menu-card-price">Bs ${item.precio}</span></p>
                        <button class="add-to-cart-btn agregar-disponible" name="${item.id_item_menu}">Agregar</button>
                    </div>
            </div>
        </div>`;
        contenedorCards.append(cardCuerpo);
    });
}

function cargarItemMenuAgregado(items) {
    const contenedorAgregados = document.getElementById("content-card-add");
    contenedorAgregados.innerHTML = "";
    items.forEach(item => {
        const cuerpoCard = `
                <div class="col-md-6 col-sm-5 col-lg-4 col-xl-4 mb-2">
                    <div class="card card-sm menu-card buscar-agregado" style="width: 10rem;">
                        <img src="${rutaLocal + item.imagen}" alt="${item.nombre}">
                        <div class="menu-card-content pb-0">
                            <h3>${item.nombre}</h3>
                            <p>Precio: <span class="menu-card-price">Bs ${item.precio}</span></p>
                            <p>Submonto: <span class="menu-card-price" id="submonto-${item.id_item_menu}">Bs ${item.sub_monto}</span></p>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control form-control-sm input-cantidad" value="${item.cantidad}" id="${item.id_item_menu}">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success btn-sm mx-1  btn-plus-agregado" name="${item.id_item_menu}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-warning btn-sm btn-minus-agregado" name="${item.id_item_menu}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center my-1">
                                    
                                   <!-- <button type="button" class="btn btn-info btn-sm mx-1 ver-item-add" name="${item.id_item_menu}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    -->
                    
                                    <button class="btn btn-danger btn-sm mx-1 eliminar-item-add" name="${item.id_item_menu}">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        `;
        contenedorAgregados.innerHTML += cuerpoCard;
    });
}

function cargarCatalogoMenu() {
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
                itemMenus = catalogoMenu[0].item_menus;
                cargarCardItemsMenu(itemMenus);
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

function cargarSelectClientes(array, id = 0) {
    const select = $("#id-cliente");
    select.empty();
    array.forEach(element => {
        let selected = "";
        if (id == element.id_cliente) {
            selected = "selected";
        }
        select.append(
            `<option value="${element.id_cliente}" ${selected}>${element.persona.nombre + " " + element.persona.paterno}</option>`
          );
    });
    select.select2({
        width: '100%', 
        theme: "classic",
        // maximumSelectionLength: 1
    });
}

function saveNotaVenta() {
    const data = {};
    const montos = montoTotal(itemsCarrito);
    data.monto = parseFloat(montos.monto_descuento);
    data.fecha = $("#fecha").val();
    data.id_cliente = $("#id-cliente").val();;
    data.id_empleado = userAutenticado.id_persona;
    data.id_tipo_pago = 1; //Corregir
    data.items_menu = itemsCarrito;
    datosEnviar = JSON.stringify(data);
    const url = rutaApiRest + "nota-venta";
    // console.log(datosEnviar);
    showLoader();
    $.ajax({
        url: url,
        type: "POST",
        data: datosEnviar,
        success: function (response) {
            console.log(response);
            const status = response.status;
            const notaVenta = response.data;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se insertó correctamente!");
                setTimeout(function(){
                    alerta.close();
                    window.open("nota-venta-comprobante-pdf/" + notaVenta.id_nota_venta, "_blank"); //abrir el comprobante
                    window.location.reload(); // recargar la pagin
                }, 1000);

                itemsCarrito = [];
                localStorage.removeItem('carritoVenta');
                cargarItemMenuAgregado(itemsCarrito);
            } else {
                alertify.alert(
                    "Error",
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

function cargarClientePromise() {
    cargarClienteEliminados();
    return new Promise((resolve, reject) => {
        const url = rutaApiRest + "cliente";
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (response) {
                console.log(response);
                const clientes = response.data;
                cargarTablaClienteVenta(clientes, false, table);
                cargarSelectClientes(clientes);
                resolve(clientes);
            },
            error: function (data, textStatus, jqXHR, error) {
                console.log(data);
                console.log(textStatus);
                console.log(jqXHR);
                console.log(error);
                reject(error); // Rechaza la Promise en caso de error
            }
        });
    });
}

async function cargarClientesAsync() {
    try {
        await cargarClientePromise();
    } catch (error) {
    }
}


function cargarTablaClienteVenta(clientes) {
    const clientePersona = [];
    const table = $("#tabla-cliente-venta");
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
        
        object.acciones = `<a data-check="${cliente.id_cliente}" class="btn btn-info btn-sm check" title="Resturar"><i class="fa fa-check"></i></a>`;
                        
        clientePersona.push(object);
    });

    table.bootstrapTable('load', clientePersona);
}


function realizarBusqueda(botonBuscar, claseBusqueda) {
    var searchText = $(botonBuscar).val().toLowerCase();

    // Iterar sobre cada elemento con la clase proporcionada y mostrar/ocultar según la búsqueda
    $(claseBusqueda).each(function () {
        var cardText = $(this).text().toLowerCase();
        if (cardText.includes(searchText)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
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
    montoInput.val(monto);

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

function verificarCantidad(idItem, cantidad) {
    const item = itemMenus.find(element => element.id_item_menu == idItem);
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

function obtenerFechaActual() {
    var fechaActual = new Date();
    var año = fechaActual.getFullYear();
    var mes = ('0' + (fechaActual.getMonth() + 1)).slice(-2); // Agrega un cero al mes si es necesario
    var dia = ('0' + fechaActual.getDate()).slice(-2); // Agrega un cero al día si es necesario

    var fechaFormateada = año + '-' + mes + '-' + dia;
    return fechaFormateada;
}

function alerta(titulo, mesaje, duracion) {
    const alerta = alertify.alert(titulo, mesaje);
    setTimeout(function(){
        alerta.close();
    }, duracion);
}



//evento select
// $('#id-cliente').on('select2:select', function (e) {
//     const data = e.params.data;
//     idCliente = parseInt(data.id);
//     console.log(idCliente);
// });