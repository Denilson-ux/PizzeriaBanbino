/**
 * Buscador funcional usando el itemMenu global del menú del día actual.
 * Se integra a la vista de cliente sin peticiones extra ni dependencias API_MENU.
 */

// Búsqueda usando los productos ya cargados (itemMenu es global)
$(document).ready(function() {
    inicializarBuscador();
});

function inicializarBuscador() {
    const searchInput = $('#search-input');
    const searchButton = $('#search-button');
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
    searchButton.on('click', function() {
        const termino = searchInput.val().trim();
        if (termino.length >= 2) {
            buscarProductos(termino);
        } else {
            alertify.warning('Por favor, ingresa al menos 2 caracteres para buscar');
        }
    });
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

function buscarProductos(termino) {
    if (typeof itemMenu === 'undefined' || itemMenu.length === 0) {
        alertify.error('No hay productos cargados en el menú...');
        return;
    }
    const terminoNormalizado = termino.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    const productosFiltrados = itemMenu.filter(producto => {
        const nombreNormalizado = producto.nombre.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        const descripcionNormalizada = (producto.descripcion || '').toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        return nombreNormalizado.includes(terminoNormalizado) || descripcionNormalizada.includes(terminoNormalizado);
    });
    mostrarResultadosBusqueda(productosFiltrados, termino);
}

function mostrarResultadosBusqueda(productos, termino) {
    const contenedor = $('#contenido-item-menu');
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
    $('.heading_container h2').html(`
        Resultados de búsqueda (${productos.length})
        <button class="btn btn-sm btn-warning ml-3" onclick="restaurarVistaOriginal()" style="vertical-align: middle;">
            <i class="fa fa-times"></i> Limpiar búsqueda
        </button>
    `);
    let html = '';
    productos.forEach(item => {
        html += generarTarjetaProductoBusqueda(item);
    });
    contenedor.html(html);
    alertify.success(`Se encontraron ${productos.length} producto(s)`);
}

function generarTarjetaProductoBusqueda(item) {
    const imagenUrl = item.imagen ? (typeof rutaLocal !== 'undefined' ? rutaLocal + item.imagen : '/feane/images/pizza-default.png') : '/feane/images/pizza-default.png';
    const precio = parseFloat(item.precio).toFixed(2);
    return `
        <div class="tarjeta-menu col-sm-6 col-lg-4">
            <div class="box">
                <div>
                    <div class="img-box img-content">
                        <img src="${imagenUrl}" alt="${item.nombre}">
                    </div>
                    <div class="detail-box">
                        <h5>${item.nombre}</h5>
                        <p>${item.descripcion}</p>
                        <div class="options">
                            <h6>$${precio}</h6>
                            <button data-carrito="${item.id_item_menu}" class="btn btn-warning btn-sm agregar-carrito" href="#" style="color: white">
                                <i class="fa fa-shopping-cart" aria-hidden="true"> Agregar al carrito</i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function restaurarVistaOriginal() {
    $('#search-input').val('');
    $('.heading_container h2').text('Menú del días');
    if (typeof cargarCardItemMenu === 'function') {
        cargarCardItemMenu(itemMenu);
    } else {
        location.reload();
    }
}
window.buscarProductos = buscarProductos;
window.restaurarVistaOriginal = restaurarVistaOriginal;
window.limpiarBusqueda = restaurarVistaOriginal;
