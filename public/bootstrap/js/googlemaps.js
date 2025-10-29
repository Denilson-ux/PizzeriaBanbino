// Google Maps initialization script
// Updated with new API key: AIzaSyAtjzxI9iVgpXTkQ6kFJ33RA5oGngdS6d0

// Callback function for Google Maps API
function initMap() {
    // This function will be called when Google Maps API is loaded
    if (typeof google !== 'undefined' && google.maps) {
        console.log('Google Maps API loaded successfully');
        // Trigger custom event to notify that Maps API is ready
        window.dispatchEvent(new Event('googleMapsLoaded'));
    } else {
        console.error('Google Maps API failed to load');
    }
}

// Function to initialize Google Maps for address input
function initAddressMap() {
    if (typeof google === 'undefined' || !google.maps) {
        console.error('Google Maps API not loaded');
        return;
    }

    const mapElement = document.getElementById('map');
    const addressInput = document.getElementById('direccion');
    
    if (!mapElement || !addressInput) {
        console.error('Map container or address input not found');
        return;
    }

    // Default map center (you can change this to your city coordinates)
    const defaultCenter = { lat: -17.7833, lng: -63.1822 }; // Santa Cruz, Bolivia coordinates
    
    // Initialize map
    const map = new google.maps.Map(mapElement, {
        zoom: 13,
        center: defaultCenter,
    });

    // Create marker
    const marker = new google.maps.Marker({
        position: defaultCenter,
        map: map,
        draggable: true,
    });

    // Initialize autocomplete
    const autocomplete = new google.maps.places.Autocomplete(addressInput, {
        componentRestrictions: { country: 'bo' }, // Restrict to Bolivia
        fields: ['address_components', 'geometry', 'name'],
    });

    // When user selects an address from autocomplete
    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) {
            console.error('No geometry data for selected place');
            return;
        }

        const location = place.geometry.location;
        map.setCenter(location);
        marker.setPosition(location);
        
        // Update hidden fields with coordinates
        const latInput = document.getElementById('latitud');
        const lngInput = document.getElementById('longitud');
        if (latInput) latInput.value = location.lat();
        if (lngInput) lngInput.value = location.lng();
    });

    // When user drags the marker
    marker.addListener('dragend', (event) => {
        const position = marker.getPosition();
        map.panTo(position);
        
        // Update hidden fields with new coordinates
        const latInput = document.getElementById('latitud');
        const lngInput = document.getElementById('longitud');
        if (latInput) latInput.value = position.lat();
        if (lngInput) lngInput.value = position.lng();

        // Reverse geocoding to get address from coordinates
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: position }, (results, status) => {
            if (status === 'OK' && results[0]) {
                addressInput.value = results[0].formatted_address;
            }
        });
    });

    // Current location button functionality
    const locationButton = document.getElementById('ubicacion-actual-btn');
    if (locationButton) {
        locationButton.addEventListener('click', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        
                        map.setCenter(userLocation);
                        marker.setPosition(userLocation);
                        
                        // Update hidden fields
                        const latInput = document.getElementById('latitud');
                        const lngInput = document.getElementById('longitud');
                        if (latInput) latInput.value = userLocation.lat;
                        if (lngInput) lngInput.value = userLocation.lng;

                        // Get address for current location
                        const geocoder = new google.maps.Geocoder();
                        geocoder.geocode({ location: userLocation }, (results, status) => {
                            if (status === 'OK' && results[0]) {
                                addressInput.value = results[0].formatted_address;
                            }
                        });
                    },
                    () => {
                        console.error('Error getting current location');
                        alert('No se pudo obtener tu ubicación actual');
                    }
                );
            } else {
                alert('Tu navegador no soporta geolocalización');
            }
        });
    }
}

// Load Google Maps API with the updated API key
function loadGoogleMapsAPI() {
    // Check if API is already loaded
    if (typeof google !== 'undefined' && google.maps) {
        console.log('Google Maps API already loaded');
        initAddressMap();
        return;
    }
    
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAtjzxI9iVgpXTkQ6kFJ33RA5oGngdS6d0&callback=initMap&libraries=places&language=es&region=BO';
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        console.error('Error loading Google Maps API - Please check API key and permissions');
    };
    document.head.appendChild(script);
}

// Initialize map when API is loaded
function initMap() {
    console.log('Google Maps API loaded successfully');
    initAddressMap();
}

// Auto-load the API when this script is executed
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadGoogleMapsAPI);
} else {
    loadGoogleMapsAPI();
}