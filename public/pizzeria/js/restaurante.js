let restaurante = {};

$(document).ready(() => {
    cargarRestaurante();
});

$(document).on("click", "#ubicacion-actual-btn", () => {
    ubicacionActualReady();
});

$("#guardar-restaurante").click(() => {
    if (validar($("#nombre")) && 
        validar($("#telefono")) && 
        validar($("#direccion")) &&
        validar($("#correo"))) {

        if (Object.keys(restaurante).length === 0) {
            saveRestaurante();
        } else {
            updateRestaurante(restaurante.id_restaurante);
        }
    } 
});

function saveRestaurante() {
    const formData = new FormData();

    formData.append('nombre', $("#nombre").val());
    formData.append('telefono', $("#telefono").val());
    formData.append('correo', $("#correo").val());
    formData.append('direccion', $("#direccion").val());
    formData.append('descripcion', $("#descripcion").val());
    formData.append('latitud', $("#latitud").val());
    formData.append('longitud', $("#longitud").val());

    const imagenInput = $("#imagen")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "restaurante";
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false, 
        processData: false,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se insertó correctamente!");
                setTimeout(function(){
                    alerta.close();
                    window.location.href = window.location.href;
                }, 1000);
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

function updateRestaurante(id) {
    const formData = new FormData();

    formData.append('nombre', $("#nombre").val());
    formData.append('telefono', $("#telefono").val());
    formData.append('correo', $("#correo").val());
    formData.append('direccion', $("#direccion").val());
    formData.append('descripcion', $("#descripcion").val());
    formData.append('latitud', $("#latitud").val());
    formData.append('longitud', $("#longitud").val());

    const imagenInput = $("#imagen")[0];
    if (imagenInput.files.length > 0) {
        formData.append('imagen', imagenInput.files[0]);
    }

    const url = rutaApiRest + "restaurante/" + id;
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        data: formData,
        contentType: false, 
        processData: false,
        success: function (response) {
            console.log(response);
            const status = response.status;
            if (status == 200) {
                const alerta = alertify.alert("Correcto", "¡Súper, se actualizó correctamente!");
                setTimeout(function(){
                    alerta.close();
                    window.location.href = window.location.href;
                }, 1000);
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

function cargarRestaurante() {
    const url = rutaApiRest + "restaurante";
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (response) {
            restaurante = response;

            $("#nombre").val(restaurante.nombre);
            $("#telefono").val(restaurante.telefono);
            $("#correo").val(restaurante.correo);
            $("#direccion").val(restaurante.direccion);
            $("#descripcion").val(restaurante.descripcion);
            $("#latitud").val(restaurante.latitud);
            $("#longitud").val(restaurante.longitud);
            vistaPreviaEdit(restaurante.imagen);
            initMap();
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


function vistaPreviaEdit(imagen) {
    try {
        const vistaPrevia = document.getElementById('vista-previa');

        vistaPrevia.src = rutaLocal + imagen;
        vistaPrevia.style.display = 'block';
    } catch (error) {
        
    }
}

/// GOOGLE MAPS
function initMap() {
    let latitud = -17.7962;
    let longitud = -63.1814;

    if ($("#latitud").val() != ""  &&  $("#longitud").val() != "") {
        latitud = parseFloat($("#latitud").val());
        longitud = parseFloat($("#longitud").val());
    }

    var myLatLng = { lat: latitud, lng: longitud };
    var mapOptions = {
      center: myLatLng,
      zoom: 8,
      streetViewControl: false,
    };
    map = new google.maps.Map(document.getElementById('map'), mapOptions);
    marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      draggable: true,
      title: 'Mi marcador',
    });

    markerListenerDragend(marker);
    mapListenerClick(map, marker);
    initAutoComplete(map, marker);
}

function mapListenerClick(map, marker) {
    map.addListener('click', function(event) {
        const clickedLat = event.latLng.lat();
        const clickedLng = event.latLng.lng();

        marker.setPosition(event.latLng);

        $("#latitud").val(clickedLat);
        $("#longitud").val(clickedLng);

        localizacionInversa(event);

    });
}

function markerListenerDragend(marker) {
    google.maps.event.addListener(marker, 'dragend', function(event) {
        const latitud = this.getPosition().lat();
        const longitud = this.getPosition().lng();

        $("#latitud").val(latitud);
        $("#longitud").val(longitud);

        localizacionInversa(event);

    });
}

function initAutoComplete(map, marker) {
    const inputDireccionCliente = document.getElementById("direccion");

    //establecer limites geográficos para Bolivia
    const center = {
        lat: -16.290154,
        lng: -63.588653
    };

    const defaultBounds = {
        north: center.lat + 0.5, 
        south: center.lat - 0.5, 
        east: center.lng + 0.5, 
        west: center.lng - 0.5, 
    };

    const options = {
        bounds: defaultBounds, 
        componentRestrictions: { country: "bo" }, 
        fields: ["address_components", "geometry", "icon", "name"],
        strictBounds: false,
        types: [],
    };

    let autoComplete = new google.maps.places.Autocomplete(inputDireccionCliente, options);

    autoComplete.addListener('place_changed', function(){
        const place = autoComplete.getPlace();

        if (place.geometry && place.geometry.location) {
            map.setCenter(place.geometry.location);
            marker.setPosition(place.geometry.location);
            $("#latitud").val(place.geometry.location.lat());
            $("#longitud").val(place.geometry.location.lng());

            smoothZoom(map, 13, 500);

        } else {
            console.log("La ubicación del lugar no está definida correctamente.");
        }
    });
}

function localizacionInversa(event) {
    // Obtener la dirección inversa utilizando Geocoding
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'location': event.latLng }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            if (results[0]) {
                const formattedAddress = results[0].formatted_address;
                $("#direccion").val(formattedAddress);
            }
        }
    });
}

// Función para realizar el zoom suave
function smoothZoom(map, targetZoom, duration) {
    const currentZoom = map.getZoom();
    const steps = Math.abs(currentZoom - targetZoom);
    const delay = duration / steps;

    if (currentZoom < targetZoom) {
      for (let i = currentZoom; i < targetZoom; i++) {
        setTimeout(function () {
          map.setZoom(i + 1);
        }, i * delay);
      }
    } else if (currentZoom > targetZoom) {
      for (let i = currentZoom; i > targetZoom; i--) {
        setTimeout(function () {
          map.setZoom(i - 1);
        }, (currentZoom - i) * delay);
      }
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

            }, function() {
                reject("Error al obtener la ubicación");
            });
        } else {
            reject("El navegador no soporta la geolocalización");
        }
    });
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

            }, function() {
                reject("Error al obtener la ubicación");
            });
        } else {
            reject("El navegador no soporta la geolocalización");
        }
    });
}

function ubicacionActualReady() {
    ubicacionActual()
        .then((posicion) => {

            localizacionInversa({ latLng: posicion });
            $("#latitud").val(posicion.lat);
            $("#longitud").val(posicion.lng);
            
            map.setCenter(posicion);
            marker.setPosition(posicion);

            smoothZoom(map, 13, 500);

            console.log('Ubicación obtenida correctamente:', posicion);
        })
        .catch((error) => {
            initMap();
            console.log("Error al obtener la ubicación:", error);
    });
}
