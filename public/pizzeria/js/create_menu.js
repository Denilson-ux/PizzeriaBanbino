let menus = [];
let itemsMenu = [];
let itemsCarrito = [];
let table = $("#tabla-menu");
let tableEliminados = $("#tabla-menu-eliminados");

// newCarrito = carrito.filter(deleteProducto => deleteProducto.id_producto != idProducto);
//                 carrito = newCarrito;

$(document).ready( () => {
    const carritoStorage = JSON.parse(localStorage.getItem('carrito'));
    if (carritoStorage) {
        if (carritoStorage.length > 0) {
            cargarItemMenuAgregado(carritoStorage);
            itemsCarrito = carritoStorage;
        }
    }
    $("#fecha").val(obtenerFechaActual());
    cargarItemsMenu();
});



$("#guardar-menu").click(() => {
    if (validar($("#nombre")) && 
        validar($("#descripcion")) &&
        validar($("#fecha")) &&
        itemsCarrito.length > 0) {
        saveMenu();
    } 
});

$(document).on("click", ".agregar-disponible", function(e) {
    const idItemMenu = this.name;
    const itemAdd = itemsMenu.find(item => item.id_item_menu == idItemMenu);
    if (!itemAdd.add) {
        itemsCarrito.push(itemAdd);
        itemAdd.add = true;
        cargarItemMenuAgregado(itemsCarrito);
        
        localStorage.setItem('carrito', JSON.stringify(itemsCarrito));
    }
});

$(document).on("click", ".eliminar-item-add", function(e) {
    const idItemMenu = this.name;
    const itemADelete = itemsMenu.find(item => item.id_item_menu == idItemMenu);
    itemADelete.add = false;
    const newItemTemp = itemsCarrito.filter(deleteItem => deleteItem.id_item_menu != idItemMenu);
    itemsCarrito = newItemTemp;
    cargarItemMenuAgregado(itemsCarrito);

    localStorage.setItem('carrito', JSON.stringify(itemsCarrito));
});

$(document).on("click", ".btn-plus-agregado", function(e) {
    const idItemMenu = this.name;
    const itemAddCantidad = itemsCarrito.find(item => item.id_item_menu == idItemMenu);
    const inputCantidad = $("#" + idItemMenu);
    const cantidadActualInput = inputCantidad.val();
    const cantidad = parseInt(cantidadActualInput) + 1;
    inputCantidad.val(cantidad);
    itemAddCantidad.cantidad = cantidad;
    
    localStorage.setItem('carrito', JSON.stringify(itemsCarrito));
});

$(document).on("click", ".btn-minus-agregado", function(e) {
    const idItemMenu = this.name;
    const itemAddCantidad = itemsCarrito.find(item => item.id_item_menu == idItemMenu);
    const inputCantidad = $("#" + idItemMenu);
    const cantidadActualInput = inputCantidad.val();
    let cantidad = parseInt(cantidadActualInput);
    if (cantidad > 1){
        cantidad -= 1;
        inputCantidad.val(cantidad);
        itemAddCantidad.cantidad = cantidad;
        localStorage.setItem('carrito', JSON.stringify(itemsCarrito));
    }
});

function cargarCardItemsMenu(items) {
    const contenedorCards = $("#content-card-disponible");
    items.forEach(item => {
        item.add = false;
        item.cantidad = 1;
        const cardCuerpo = `
        <div class="col-md-6 col-sm-5 col-lg-4 col-xl-4 mb-4">
            <div class="card card-sm menu-card" style="width: 10rem;">
                <img src="${rutaLocal + item.imagen}" alt="${item.nombre}">
                    <div class="menu-card-content">
                        <h3>${item.nombre}</h3>
                        <p>${item.descripcion}</p>
                        <p>Precio: <span class="menu-card-price">Bs ${item.precio}</span></p>
                        <button class="add-to-cart-btn agregar-disponible" name="${item.id_item_menu}">Agregar</button>
                    </div>
            </div>
        </div>
        `;
        contenedorCards.append(cardCuerpo);
    });
}

function cargarItemMenuAgregado(items) {
    const contenedorAgregados = document.getElementById("content-card-add");
   contenedorAgregados.innerHTML = "";
    items.forEach(item => {
        const cuerpoCard = `
                <div class="col-md-6 col-sm-5 col-lg-4 col-xl-4 mb-4">
                    <div class="card card-sm menu-card agregado" style="width: 10rem;">
                        <img src="${rutaLocal + item.imagen}" alt="${item.nombre}">
                        <div class="menu-card-content">
                            <h3>${item.nombre}</h3>
                            <p>Precio: <span class="menu-card-price">Bs ${item.precio}</span></p>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control form-control-sm" value="${item.cantidad}" id="${item.id_item_menu}">

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
                                    
                                   <button type="button" class="btn btn-info btn-sm mx-1 ver-item-add" name="${item.id_item_menu}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                    
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


function cargarItemsMenu() {
    const url = rutaApiRest + "item-menu";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            itemsMenu = response.data;
            cargarCardItemsMenu(itemsMenu);
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}

function saveMenu() {
    const data = {};
    data.nombre = $("#nombre").val();
    data.descripcion = $("#descripcion").val();
    data.fecha = $("#fecha").val();
    data.items_menu = itemsCarrito;
    datosEnviar = JSON.stringify(data);
    const url = rutaApiRest + "menu";
    console.log(datosEnviar);
    showLoader();
    $.ajax({
        url: url,
        type: "POST",
        data: datosEnviar,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se insertó correctamente!");
                setTimeout(function(){
                    alerta.close();
                }, 1000);

                $("#nombre").val("");
                $("#modal-nuevo-rol").modal('hide');
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


function obtenerFechaActual() {
    var fechaActual = new Date();
    var año = fechaActual.getFullYear();
    var mes = ('0' + (fechaActual.getMonth() + 1)).slice(-2); // Agrega un cero al mes si es necesario
    var dia = ('0' + fechaActual.getDate()).slice(-2); // Agrega un cero al día si es necesario

    var fechaFormateada = año + '-' + mes + '-' + dia;
    return fechaFormateada;
}

