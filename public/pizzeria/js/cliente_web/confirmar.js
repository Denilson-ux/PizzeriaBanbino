let map;
let marker;
let metodoPago = 1; //efectivo
let total = 0;
let googleMapsLoaded = false;
let paypalInitialized = false;
let paypalRetries = 0;
const MAX_PAYPAL_RETRIES = 5;

$(document).ready(function () {
    cargarDatosCliente();
    cargarDetalleProducto();    
    
    // Wait for Google Maps to load before initializing map
    if (typeof google !== 'undefined' && google.maps) {
        initMapConfirmar();
    } else {
        // Listen for Google Maps API load event
        window.addEventListener('googleMapsLoaded', () => {
            initMapConfirmar();
        });
        // Fallback: check periodically if Google Maps is loaded
        const checkGoogleMaps = setInterval(() => {
            if (typeof google !== 'undefined' && google.maps) {
                clearInterval(checkGoogleMaps);
                if (!googleMapsLoaded) {
                    initMapConfirmar();
                }
            }
        }, 100);
    }

    // Initialize PayPal when page is ready
    initPayPalButtons();

    $("#registrarme-nav").addClass("d-none");
    $("#nav-carrito-search").addClass("d-none");
});

// Enhanced PayPal Initialization with retries
function initPayPalButtons() {
    console.log(`Intentando inicializar PayPal (intento ${paypalRetries + 1}/${MAX_PAYPAL_RETRIES})`);
    
    if (paypalRetries >= MAX_PAYPAL_RETRIES) {
        console.error('PayPal SDK no se pudo cargar después de múltiples intentos');
        showPayPalError();
        return;
    }
    
    // Check if PayPal SDK is loaded
    if (typeof paypal !== 'undefined' && paypal.Buttons) {
        if (!paypalInitialized) {
            console.log('✅ PayPal SDK detectado, inicializando botones...');
            renderPayPalButtons();
            paypalInitialized = true;
            return;
        }
    }
    
    // PayPal not ready, increment retry counter and try again
    paypalRetries++;
    const retryDelay = Math.min(1000 * paypalRetries, 5000); // Progressive delay, max 5s
    
    console.warn(`⏳ PayPal SDK no disponible, reintentando en ${retryDelay}ms...`);
    
    setTimeout(() => {
        initPayPalButtons();
    }, retryDelay);
}

// Render PayPal Buttons with enhanced error handling
function renderPayPalButtons() {
    try {
        console.log('🔄 Renderizando botones de PayPal...');
        
        paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'blue',
                shape: 'rect',
                label: 'pay',
                height: 45
            },
            
            createOrder: function(data, actions) {
                const montoDolares = montoDolar(total);
                
                console.log('💰 Creando orden PayPal:', {
                    bolivianos: total,
                    dolares: montoDolares
                });
                
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: montoDolares,
                            currency_code: 'USD'
                        },
                        description: `Pizzería Bambino - Pedido ${Date.now()}`,
                        custom_id: `PIZZA-${Date.now()}`
                    }]
                });
            },
            
            onApprove: function(data, actions) {
                console.log('✅ PayPal onApprove triggered:', data);
                
                return actions.order.capture().then(function(details) {
                    console.log('🎉 ¡Pago completado con PayPal!', details);
                    
                    if (details.status === "COMPLETED") {
                        const clienteInfo = `${details.payer.name.given_name} ${details.payer.name.surname}`;
                        const descripcionPago = `PayPal LIVE - ${clienteInfo} (${details.payer.email_address})`;
                        
                        sweentAlert("top-end", "success", `¡Pago completado! Recibido de ${details.payer.name.given_name}`, 3000);
                        
                        // Guardar el pedido con información de PayPal
                        savePedido(details.id, descripcionPago);
                    } else {
                        sweentAlert("top-end", "warning", "El pago está siendo procesado...", 2000);
                    }
                }).catch(function(error) {
                    console.error('❌ Error capturando el pago:', error);
                    sweentAlert("top-end", "error", "Error al completar el pago. Intenta nuevamente.", 3000);
                });
            },
            
            onCancel: function(data) {
                console.log('⚠️ Pago cancelado por el usuario:', data);
                sweentAlert("top-end", "warning", "Pago cancelado por el usuario", 2000);
            },
            
            onError: function(err) {
                console.error('❌ Error en PayPal:', err);
                sweentAlert("top-end", "error", "Error al procesar el pago. Por favor intenta de nuevo.", 3000);
            }
            
        }).render('#paypal-button-container').then(() => {
            console.log('✅ PayPal buttons rendered successfully');
        }).catch((error) => {
            console.error('❌ Error rendering PayPal buttons:', error);
            showPayPalError();
        });
        
    } catch (error) {
        console.error('❌ Error inicializando PayPal:', error);
        showPayPalError();
    }
}

// Enhanced PayPal Error Display
function showPayPalError() {
    const paypalContainer = document.getElementById('paypal-button-container');
    if (paypalContainer) {
        paypalContainer.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>PayPal no disponible</strong><br>
                <small>Verifica tu conexión a internet e intenta recargar la página.</small><br>
                <button onclick="window.location.reload()" class="btn btn-sm btn-outline-danger mt-2">
                    <i class="fas fa-redo"></i> Recargar página
                </button>
            </div>
        `;
    }
}

$(document).on("click", "#ubicacion-actual-btn", () => {
    if (googleMapsLoaded) {
        ubicacionActualReady();
    } else {
        sweentAlert("top-end", "warning", "Esperando a que se cargue Google Maps...", 1500);
    }
});

$(document).on("click", "#seguir-comprando", () => {
    const enlaceTemporal = document.createElement('a');
    enlaceTemporal.href = rutaLocal;
    enlaceTemporal.click();
});

$(document).on("click", "#confirmar-pedido", () => {
    if (validar($("#direccion")) && 
        validar($("#latitud")) &&
        validar($("#longitud"))) {
        savePedido();
    } else {
        if (!validar($("#latitud"))  ||  !validar($("#longitud"))) {
            sweentAlert("top-end", "error", "Necesitas marcar tu ubicación en el mapa.", 1500);
        }
    }
});

function savePedido(nro_transaccion = null, descripcion_pago = null) {
    let carritomall = JSON.parse(localStorage.getItem('carritomall'));
    const clienteMall = JSON.parse(localStorage.getItem('clientemall'));
    
    // Validar que tenemos la información necesaria
    if (!clienteMall || !clienteMall.id_cliente) {
        sweentAlert("top-end", "error", "Error: Información de cliente no encontrada. Inicia sesión nuevamente.", 3000);
        return;
    }
    
    if (!carritomall || carritomall.length === 0) {
        sweentAlert("top-end", "error", "Error: No hay productos en el carrito.", 2000);
        return;
    }
    
    carritomall = castearCarrito(carritomall);
    const data = {};
    const montos = montoTotal(carritomall, 0);
    
    data.monto = parseFloat(montos.monto);
    data.fecha = obtenerFechaActual();
    data.id_repartidor = null;
    data.id_cliente = parseInt(clienteMall.id_cliente);
    data.id_tipo_pago = metodoPago; 
    data.estado_pedido = "Pendiente";
    data.nro_transaccion = nro_transaccion;
    data.descripcion_pago = descripcion_pago;
    data.latitud = parseFloat($("#latitud").val());
    data.longitud = parseFloat($("#longitud").val());
    data.referencia = $("#direccion").val() || "Dirección no especificada";
    data.items_menu = carritomall;
    
    console.log('📦 Datos del pedido a enviar:', data);
    
    const datosEnviar = JSON.stringify(data);
    const url = rutaApiRest + "pedido";
    showLoader();
    
    $.ajax({
        url: url,
        type: "POST",
        data: datosEnviar,
        contentType: "application/json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        success: function (response) {
            console.log('✅ Respuesta del servidor:', response);
            const status = response.status;
            if (status == 200) {
                const data = response.data;
                sweentAlert("top-end", "success", "Pedido realizado correctamente", 1500);
                localStorage.removeItem('carritomall');
                
                if (data && data.id_pedido) {
                    setTimeout(() => {
                        window.location.href = rutaLocal + "detalle/" + data.id_pedido;
                    }, 2000);
                } else {
                    setTimeout(() => {
                        window.location.href = rutaLocal;
                    }, 2000);
                }
            } else {
                console.error('❌ Error en respuesta:', response);
                sweentAlert("top-end", "error", response.message || "Ocurrió un problema al registrar tu pedido", 3000);
            }
            hideLoader();
        },
        error: function (xhr, textStatus, error) {
            console.error('❌ Error AJAX:', {
                xhr: xhr,
                textStatus: textStatus,
                error: error,
                responseText: xhr.responseText
            });
            
            let mensajeError = "Error al procesar tu pedido";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                mensajeError = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    mensajeError = errorResponse.message || mensajeError;
                } catch (e) {
                    mensajeError = "Error de comunicación con el servidor";
                }
            }
            
            sweentAlert("top-end", "error", mensajeError, 4000);
            hideLoader();
        }
    });
}

function cargarDatosCliente() {
    const clientemall = JSON.parse(localStorage.getItem('clientemall'));
    if (!clientemall) {
        sweentAlert("top-end", "error", "No hay información de cliente. Inicia sesión.", 3000);
        setTimeout(() => {
            window.location.href = rutaLocal + "form";
        }, 3000);
        return;
    }
    
    $("#nombre-usuario").text(clientemall.user ? clientemall.user.name : clientemall.nombre || "Cliente");
    $("#nombre").val(clientemall.nombre || "");
    $("#paterno").val(clientemall.paterno || "");
    $("#telefono").val(clientemall.telefono || "");
    $("#correo").val(clientemall.correo || "");
    
    console.log('👤 Cliente cargado:', clientemall);
}

function cargarDetalleProducto() {
    const carritomall = JSON.parse(localStorage.getItem('carritomall'));
    if (!carritomall || carritomall.length === 0) {
        sweentAlert("top-end", "error", "No hay productos en el carrito", 2000);
        setTimeout(() => {
            window.location.href = rutaLocal;
        }, 2000);
        return;
    }
    
    const contenedor = $("#detalle-productos");
    const cabecera = `
        <div class="col-6"><strong>Producto</strong></div>
        <div class="col-6 text-right"><strong>Subtotal</strong></div>
    `;
    contenedor.append(cabecera);
    total = 0;
    
    carritomall.forEach(element => {
        total += parseFloat(element.sub_monto || 0);
        const cuerpo = `
            <div class="col-6"><span>${element.nombre || 'Producto'}</span></div>
            <div class="col-2 text-center"><span> x${element.cantidad || 1}</span></div>
            <div class="col-4 text-right"><span>${element.sub_monto || 0} Bs.</span></div>
        `;
        contenedor.append(cuerpo);
    });
    
    $("#total").text(total + " " + "Bs.");
    $("#price").text("$ " + montoDolar(total));
    
    console.log(`🛒 Total del carrito: ${total} Bs. ($ ${montoDolar(total)})`);
}

function montoTotal(array, descuentoCliente) {
    let monto = 0;
    const descuento = descuentoCliente;

    array.forEach(element => {
        monto += parseFloat(element.sub_monto || 0);
    });

    return {
        monto: monto,
        monto_descuento: 0 
    };
}

function montoDolar(monto) {
    return (monto / 6.96).toFixed(2);
}

function castearCarrito(carrito) {
    carrito.forEach(element => {
        if (element.pivot) {
            element.id_menu = element.pivot.id_menu;
        }
        element.id_item_menu = element.id_item_menu || element.id;
        element.cantidad = element.cantidad || 1;
        element.sub_monto = element.sub_monto || 0;
    });
    return carrito;
}

function obtenerFechaActual() {
    var fechaActual = new Date();
    var año = fechaActual.getFullYear();
    var mes = ('0' + (fechaActual.getMonth() + 1)).slice(-2);
    var dia = ('0' + fechaActual.getDate()).slice(-2);
    var fechaFormateada = año + '-' + mes + '-' + dia;
    return fechaFormateada;
}

$("input[type='radio']").change(function () {
    if ($(this).is(":checked")) {
        var radioId = $(this).attr("id");
        if (radioId == "pago-paypal") {
            $("#price").removeClass("d-none");
            $("#label-price").removeClass("d-none");
            $("#paypal-button-container").removeClass("d-none");
            $("#container-button-confirmar").addClass("d-none");
            metodoPago = 2;
            
            console.log('💳 Método de pago cambiado a PayPal');
            
            // Initialize PayPal if not done yet
            if (!paypalInitialized) {
                paypalRetries = 0; // Reset retry counter
                initPayPalButtons();
            }
        } else {
            $("#price").addClass("d-none");
            $("#label-price").addClass("d-none");
            $("#paypal-button-container").addClass("d-none");
            $("#container-button-confirmar").removeClass("d-none");
            metodoPago = 1;
            
            console.log('💰 Método de pago cambiado a Efectivo');
        }
    }
});

/// GOOGLE MAPS - Renamed to avoid conflicts
function initMapConfirmar() {
    if (typeof google === 'undefined' || !google.maps) {
        console.error('Google Maps API not available');
        showMapError();
        return;
    }

    if (googleMapsLoaded) {
        console.log('Map already initialized, skipping...');
        return;
    }

    try {
        const latitud = -17.7962;
        const longitud = -63.1814;
        const myLatLng = { lat: latitud, lng: longitud };
        
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error('Map container not found');
            return;
        }

        const mapOptions = {
            center: myLatLng,
            zoom: 13,
            streetViewControl: false,
            mapTypeControl: true,
            fullscreenControl: true,
            zoomControl: true
        };
        
        map = new google.maps.Map(mapContainer, mapOptions);
        
        marker = new google.maps.Marker({
            position: myLatLng,
            map: map,
            draggable: true,
            title: 'Arrastra para cambiar ubicación'
        });

        markerListenerDragend(marker);
        mapListenerClick(map, marker);
        initAutoComplete(map, marker);
        
        googleMapsLoaded = true;
        console.log('🗺️ Map initialized successfully');
        
    } catch (error) {
        console.error('Error initializing map:', error);
        showMapError();
    }
}

function showMapError() {
    const mapContainer = document.getElementById('map');
    if (mapContainer) {
        mapContainer.innerHTML = `
            <div style="display: flex; align-items: center; justify-content: center; height: 100%; background-color: #f5f5f5; color: #666; flex-direction: column; padding: 20px; text-align: center; border-radius: 8px;">
                <h5 style="color: #d32f2f; margin-bottom: 10px;">⚠️ Error del Mapa</h5>
                <p style="margin: 5px 0;">No se pudo cargar Google Maps.</p>
                <p style="margin: 5px 0; font-size: 12px;">Puedes escribir tu dirección manualmente en el campo de arriba.</p>
                <button onclick="window.location.reload()" style="margin-top: 10px; padding: 8px 16px; background-color: #1976d2; color: white; border: none; border-radius: 4px; cursor: pointer;">Recargar Página</button>
            </div>
        `;
    }
}

function mapListenerClick(map, marker) {
    map.addListener('click', function(event) {
        try {
            const clickedLat = event.latLng.lat();
            const clickedLng = event.latLng.lng();

            marker.setPosition(event.latLng);
            $("#latitud").val(clickedLat);
            $("#longitud").val(clickedLng);
            localizacionInversa(event);
        } catch (error) {
            console.error('Error in map click handler:', error);
        }
    });
}

function markerListenerDragend(marker) {
    marker.addListener('dragend', function(event) {
        try {
            const position = this.getPosition();
            const latitud = position.lat();
            const longitud = position.lng();

            $("#latitud").val(latitud);
            $("#longitud").val(longitud);
            
            const geoEvent = {
                latLng: position
            };
            localizacionInversa(geoEvent);
        } catch (error) {
            console.error('Error in marker drag handler:', error);
        }
    });
}

function initAutoComplete(map, marker) {
    try {
        const inputDireccionCliente = document.getElementById("direccion");
        if (!inputDireccionCliente) {
            console.error('Address input not found');
            return;
        }

        if (!google.maps.places) {
            console.error('Places library not loaded');
            return;
        }

        const boliviaBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(-22.896133, -69.640388),
            new google.maps.LatLng(-9.680567, -57.453803)
        );

        const options = {
            bounds: boliviaBounds,
            componentRestrictions: { country: "bo" },
            fields: ["address_components", "geometry", "name"],
            strictBounds: false,
        };

        const autoComplete = new google.maps.places.Autocomplete(inputDireccionCliente, options);

        autoComplete.addListener('place_changed', function(){
            try {
                const place = autoComplete.getPlace();

                if (place.geometry && place.geometry.location) {
                    const location = place.geometry.location;
                    map.setCenter(location);
                    marker.setPosition(location);
                    
                    $("#latitud").val(location.lat());
                    $("#longitud").val(location.lng());
                    
                    map.setZoom(15);
                } else {
                    console.log("La ubicación del lugar no está definida correctamente.");
                    sweentAlert("top-end", "warning", "No se pudo obtener la ubicación del lugar seleccionado.", 2000);
                }
            } catch (error) {
                console.error('Error in place_changed handler:', error);
            }
        });
    } catch (error) {
        console.error('Error initializing autocomplete:', error);
    }
}

function localizacionInversa(event) {
    try {
        if (!event.latLng) {
            console.error('No latLng in event');
            return;
        }
        
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ 'location': event.latLng }, function(results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    const formattedAddress = results[0].formatted_address;
                    $("#direccion").val(formattedAddress);
                } else {
                    console.log('No se encontraron resultados de geocoding');
                }
            } else {
                console.error('Geocoder failed due to: ' + status);
            }
        });
    } catch (error) {
        console.error('Error in reverse geocoding:', error);
    }
}

function ubicacionActual() {
    return new Promise(function(resolve, reject) {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                resolve(pos);
            }, function(error) {
                let errorMessage = "Error al obtener la ubicación: ";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += "Permiso denegado. Por favor, permite el acceso a tu ubicación.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += "Ubicación no disponible";
                        break;
                    case error.TIMEOUT:
                        errorMessage += "Tiempo de espera agotado";
                        break;
                    default:
                        errorMessage += "Error desconocido";
                        break;
                }
                reject(errorMessage);
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 60000
            });
        } else {
            reject("Tu navegador no soporta geolocalización");
        }
    });
}

function ubicacionActualReady() {
    if (!googleMapsLoaded) {
        sweentAlert("top-end", "warning", "El mapa aún se está cargando, intenta de nuevo en un momento.", 2000);
        return;
    }
    
    ubicacionActual()
        .then((posicion) => {
            try {
                const latLng = new google.maps.LatLng(posicion.lat, posicion.lng);
                localizacionInversa({ latLng: latLng });
                
                $("#latitud").val(posicion.lat);
                $("#longitud").val(posicion.lng);
                
                map.setCenter(posicion);
                marker.setPosition(posicion);
                map.setZoom(15);
                
                console.log('📍 Ubicación obtenida correctamente:', posicion);
                sweentAlert("top-end", "success", "Ubicación actual obtenida", 1500);
            } catch (error) {
                console.error('Error processing current location:', error);
            }
        })
        .catch((error) => {
            console.log("Error al obtener la ubicación:", error);
            sweentAlert("top-end", "error", error, 3000);
        });
}

function sweentAlert(posicion, estado, mensaje, duracion) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            position: posicion,
            icon: estado,
            title: mensaje,
            showConfirmButton: false,
            timer: duracion,
            customClass: {
                title: 'my-custom-font-class'
            }
        });
    } else {
        alert(mensaje);
    }
}