let menu = [];
let itemsMenu = [];
let itemsCarrito = [];
let table = $("#tabla-menu");
let tableEliminados = $("#tabla-menu-eliminados");

$(document).ready( () => {
    const menuStorage = JSON.parse(localStorage.getItem('menuEdit'));
    if (menuStorage) {
        menu = menuStorage;
        itemsCarrito = menuStorage.item_menus;
        if (itemsCarrito.length > 0) {
            cargarItemMenuAgregado(itemsCarrito);
        }

        $("#nombre").val(menu.nombre);
        $("#descripcion").val(menu.descripcion);
        $("#fecha").val(menu.fecha);
    }
    cargarItemsMenu();
});

$("#actualizar-menu").click(() => {
    if (validar($("#nombre")) && 
        validar($("#descripcion")) &&
        itemsCarrito.length > 0) {
        updateMenu();
    } 
});

$("#copiar-catalogo-menu").click(() => {
    alertify.confirm('Copiar catálogo', '¿Seguro de copiar el catálogo?', 
        function () {
            if (validar($("#nombre")) && 
                validar($("#descripcion")) &&
                validar($("#fecha")) &&
                itemsCarrito.length > 0) {
                saveMenu();
            }
        },
        function () {
            alertify.error('cancelado');
        }
    );
});

$('#buscar').on('keyup', function () {
    realizarBusqueda('#buscar', '.buscar-disponible');
});

$('#buscar-add').on('keyup', function () {
    realizarBusqueda('#buscar-add', '.buscar-agregado');
});

$(document).on("click", ".agregar-disponible", function(e) {
    const idItemMenu = this.name;
    const itemAdd = itemsMenu.find(item => item.id_item_menu == idItemMenu);
    if (!itemAdd.add) {
        itemsCarrito.push(itemAdd);
        itemAdd.add = true;
        cargarItemMenuAgregado(itemsCarrito);
        
        localStorage.setItem('itemsEdit', JSON.stringify(itemsCarrito));
    }
});

$(document).on("click", ".eliminar-item-add", function(e) {
    const idItemMenu = this.name;
    const itemADelete = itemsMenu.find(item => item.id_item_menu == idItemMenu);
    itemADelete.add = false;
    const newItemTemp = itemsCarrito.filter(deleteItem => deleteItem.id_item_menu != idItemMenu);
    itemsCarrito = newItemTemp;
    cargarItemMenuAgregado(itemsCarrito);

    localStorage.setItem('itemsEdit', JSON.stringify(itemsCarrito));
});

$(document).on("click", ".btn-plus-agregado", function(e) {
    const idItemMenu = this.name;
    const itemAddCantidad = itemsCarrito.find(item => item.id_item_menu == idItemMenu);
    const inputCantidad = $("#" + idItemMenu);
    const cantidadActualInput = inputCantidad.val();
    const cantidad = parseInt(cantidadActualInput) + 1;
    inputCantidad.val(cantidad);
    itemAddCantidad.cantidad = cantidad;
    
    localStorage.setItem('itemsEdit', JSON.stringify(itemsCarrito));
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
        localStorage.setItem('itemsEdit', JSON.stringify(itemsCarrito));
    }
});

$(document).on("keyup", ".input-cantidad", function(e) {
    const id = this.id;
    const itemAddCantidad = itemsCarrito.find(item => item.id_item_menu == id);
    const inputCantidad = $("#" + id);
    const cantidad = parseInt(inputCantidad.val());
    if ( cantidad <= 0) {
        alerta("Error", "No se permiten cantidades negativas", 1000);
        inputCantidad.val(1);
    } else {
        itemAddCantidad.cantidad = cantidad;
    }
});

function cargarDatos() {
    itemsCarrito.forEach(element => {
        element.cantidad = element.cantidad == null ? element.pivot.cantidad : element.cantidad;
        itemsMenu.forEach(element2 => {
            if (element.id_item_menu == element2.id_item_menu) {
                element2.add = true;
            }
        });
    });
}

function cargarCardItemsMenu(items) {
    const contenedorCards = $("#content-card-disponible");
    items.forEach(item => {
        item.add = false;
        item.cantidad = 1;
        const cardCuerpo = `
        <div class="col-md-6 col-sm-5 col-lg-4 col-xl-4 mb-4">
            <div class="card card-sm menu-card buscar-disponible" style="width: 10rem;">
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
                    <div class="card card-sm menu-card buscar-agregado" style="width: 10rem;">
                        <img src="${rutaLocal + item.imagen}" alt="${item.nombre}">
                        <div class="menu-card-content">
                            <h3>${item.nombre}</h3>
                            <p>Precio: <span class="menu-card-price">Bs ${item.precio}</span></p>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control form-control-sm input-cantidad" value="${item.cantidad != null ? item.cantidad : item.pivot.cantidad}" id="${item.id_item_menu}">

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
            cargarDatos();
        },
        error: function (data, textStatus, jqXHR, error) {
            console.log(data);
            console.log(textStatus);
            console.log(jqXHR);
            console.log(error);
        }

    });
}

function updateMenu() {
    const data = {};
    const idMenu = menu.id_menu;
    data.nombre = $("#nombre").val();
    data.descripcion = $("#descripcion").val();
    data.items_menu = itemsCarrito;
    datosEnviar = JSON.stringify(data);

    const url = rutaApiRest + "menu/" + idMenu;
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
                const alerta = alertify.alert("Correcto", "¡Súper, se actualizó correctamente!");
                setTimeout(function(){
                    alerta.close();
                    redirect("admin/menu");
                }, 1000);



                itemsCarrito = [];
                localStorage.removeItem('itemsEdit');
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

function saveMenu() {
    const data = {};
    data.nombre = $("#nombre").val();
    data.descripcion = $("#descripcion").val();
    data.fecha = obtenerFechaActual();
    data.items_menu = itemsCarrito;
    datosEnviar = JSON.stringify(data);
    const url = rutaApiRest + "menu";
    // console.log(datosEnviar);
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
                    redirect("admin/menu");
                }, 1000);

                $("#nombre").val("");

                itemsCarrito = [];
                localStorage.removeItem('carrito');
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

function obtenerFechaActual() {
    var fechaActual = new Date();
    var año = fechaActual.getFullYear();
    var mes = ('0' + (fechaActual.getMonth() + 1)).slice(-2); // Agrega un cero al mes si es necesario
    var dia = ('0' + fechaActual.getDate()).slice(-2); // Agrega un cero al día si es necesario

    var fechaFormateada = año + '-' + mes + '-' + dia;
    return fechaFormateada;
}

function redirect(rutaRelativa) {
    const enlaceTemporal = document.createElement('a');
    enlaceTemporal.href = rutaLocal + rutaRelativa;
    enlaceTemporal.click();
}


