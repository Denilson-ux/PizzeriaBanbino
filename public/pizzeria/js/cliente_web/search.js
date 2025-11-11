/**
 * Sistema de búsqueda de productos del catálogo del menú
 * Permite buscar pizzas y productos en tiempo real
 */

let todosLosProductos = [];
let productosFiltrados = [];

// Inicializar la búsqueda al cargar la página
$(document).ready(function() {
    cargarTodosLosProductos();
    inicializarBuscador();
});

/**
 * Carga todos los productos disponibles del menú
 */
function cargarTodosLosProductos() {
    // Obtener el menú del día actual
    const fechaActual = new Date().toISOString().split('T')[0];
    
    $.ajax({
        url: `${API_MENU}/fecha/${fechaActual}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                // Extraer todos los items del menú
                todosLosProductos = [];
                response.data.forEach(menu => {
                    if (menu.items_menu) {
                        menu.items_menu.forEach(item => {
                            todosLosProductos.push({
                                id: item.id_item_menu,
                                nombre: item.nombre,
                                descripcion: item.descripcion || '',
                                precio: parseFloat(item.precio) || 0,
                                imagen: item.imagen || '',
                                tipo: item.tipo_menu ? item.tipo_menu.tipo : 'General',
                                cantidad: item.pivot ? item.pivot.cantidad : 0
                            });
                        });
                    }
                });
                console.log('Productos cargados:', todosLosProductos.length);
            }
        },
        error: function(error) {
            console.error('Error al cargar productos:', error);
        }
    });
}

/**
 * Inicializa los eventos del buscador
 */
function inicializarBuscador() {
    const searchInput = $('#search-input');
    const searchButton = $('#search-button');
    
    // Búsqueda al escribir (con debounce)
    let timeoutId;
    searchInput.on('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            const termino = $(this).val().trim();
            if (termino.length >= 2) {
                buscarProductos(termino);
            } else if (termino.length === 0) {
                restaurarVistaOriginal();
            }
        }, 500);
    });
    
    // Búsqueda al hacer clic en el botón
    searchButton.on('click', function() {
        const termino = searchInput.val().trim();
        if (termino.length >= 2) {
            buscarProductos(termino);
        } else {
            alertify.warning('Por favor, ingresa al menos 2 caracteres para buscar');
        }
    });
    
    // Búsqueda al presionar Enter
    searchInput.on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const termino = $(this).val().trim();
            if (termino.length >= 2) {
                buscarProductos(termino);
            }
        }
    });
}

/**
 * Realiza la búsqueda de productos
 * @param {string} termino - Término de búsqueda
 */
function buscarProductos(termino) {
    if (todosLosProductos.length === 0) {
        alertify.error('Cargando productos, por favor espera...');
        return;
    }
    
    // Normalizar el término de búsqueda
    const terminoNormalizado = termino.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    
    // Filtrar productos
    productosFiltrados = todosLosProductos.filter(producto => {
        const nombreNormalizado = producto.nombre.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const descripcionNormalizada = producto.descripcion.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const tipoNormalizado = producto.tipo.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        
        return nombreNormalizado.includes(terminoNormalizado) || 
               descripcionNormalizada.includes(terminoNormalizado) ||
               tipoNormalizado.includes(terminoNormalizado);
    });
    
    // Mostrar resultados
    mostrarResultadosBusqueda(productosFiltrados, termino);
}

/**
 * Muestra los resultados de la búsqueda
 * @param {Array} productos - Array de productos encontrados
 * @param {string} termino - Término buscado
 */
function mostrarResultadosBusqueda(productos, termino) {
    const contenedor = $('#contenido-item-menu');
    
    // Desplazarse a la sección de productos
    $('html, body').animate({
        scrollTop: $('#section-items').offset().top - 100
    }, 500);
    
    if (productos.length === 0) {
        contenedor.html(`
            <div class="col-12 text-center py-5">
                <i class="fa fa-search" style="font-size: 48px; color: #ff6b35; margin-bottom: 20px;"></i>
                <h3 style="color: white;">No se encontraron resultados</h3>
                <p style="color: #ddd;">No hay productos que coincidan con "<strong>${termino}</strong>"</p>
                <button class="btn btn-warning" onclick="restaurarVistaOriginal()">
                    <i class="fa fa-arrow-left"></i> Volver al menú completo
                </button>
            </div>
        `);
        return;
    }
    
    // Actualizar título de la sección
    $('.heading_container h2').html(`
        Resultados de búsqueda (${productos.length})
        <button class="btn btn-sm btn-warning ml-3" onclick="restaurarVistaOriginal()" style="vertical-align: middle;">
            <i class="fa fa-times"></i> Limpiar búsqueda
        </button>
    `);
    
    // Renderizar productos
    let html = '';
    productos.forEach(producto => {
        html += generarTarjetaProducto(producto);
    });
    
    contenedor.html(html);
    
    // Mostrar mensaje de éxito
    alertify.success(`Se encontraron ${productos.length} producto(s)`);
}

/**
 * Genera el HTML de una tarjeta de producto
 * @param {Object} producto - Datos del producto
 * @returns {string} HTML de la tarjeta
 */
function generarTarjetaProducto(producto) {
    const imagenUrl = producto.imagen ? `/storage/${producto.imagen}` : '/feane/images/pizza-default.png';
    const precio = producto.precio.toFixed(2);
    
    return `
        <div class="col-sm-6 col-lg-4 all ${producto.tipo.toLowerCase()}">
            <div class="box">
                <div>
                    <div class="img-box">
                        <img src="${imagenUrl}" alt="${producto.nombre}" onerror="this.src='/feane/images/pizza-default.png'">
                    </div>
                    <div class="detail-box">
                        <h5>${producto.nombre}</h5>
                        <p>${producto.descripcion}</p>
                        <div class="options">
                            <h6>$${precio}</h6>
                            <button class="btn btn-warning" onclick="agregarAlCarrito(${producto.id}, '${producto.nombre}', ${producto.precio})">
                                <i class="fa fa-shopping-cart"></i> Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

/**
 * Restaura la vista original del menú
 */
function restaurarVistaOriginal() {
    $('#search-input').val('');
    productosFiltrados = [];
    
    // Restaurar título original
    $('.heading_container h2').text('Menú del días');
    
    // Recargar el menú original
    if (typeof cargarMenuDelDia === 'function') {
        cargarMenuDelDia();
    } else {
        location.reload();
    }
}

/**
 * Limpia el campo de búsqueda
 */
function limpiarBusqueda() {
    restaurarVistaOriginal();
}

// Exportar funciones para uso global
window.buscarProductos = buscarProductos;
window.restaurarVistaOriginal = restaurarVistaOriginal;
window.limpiarBusqueda = limpiarBusqueda;