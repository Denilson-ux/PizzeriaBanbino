// Google Maps initialization script - Fixed infinite loop issue
// API Key: AIzaSyAtjzxI9iVgpXTkQ6kFJ33RA5oGngdS6d0

// Global flag to prevent multiple initializations
window.googleMapsApiLoaded = false;

// Global callback function for Google Maps API
function initMap() {
    if (window.googleMapsApiLoaded) {
        console.log('Google Maps API already initialized, skipping...');
        return;
    }
    
    console.log('Google Maps API loaded successfully');
    window.googleMapsApiLoaded = true;
    
    // Trigger custom event to notify that Maps API is ready
    if (typeof window !== 'undefined') {
        window.dispatchEvent(new Event('googleMapsLoaded'));
    }
}

// Load Google Maps API - simplified version without conflicts
function loadGoogleMapsAPI() {
    // Check if API is already loaded
    if (typeof google !== 'undefined' && google.maps) {
        console.log('Google Maps API already loaded');
        if (!window.googleMapsApiLoaded) {
            initMap();
        }
        return;
    }
    
    // Check if script is already being loaded
    if (document.querySelector('script[src*="maps.googleapis.com"]')) {
        console.log('Google Maps script already loading...');
        return;
    }
    
    // Create script tag for Google Maps API
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAtjzxI9iVgpXTkQ6kFJ33RA5oGngdS6d0&callback=initMap&libraries=places&v=weekly&language=es&region=BO';
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        console.error('Error loading Google Maps API - Check API key and enabled services');
        // Show user-friendly error message
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; background-color: #f5f5f5; color: #666; flex-direction: column; padding: 20px; text-align: center;">
                    <h5 style="color: #d32f2f; margin-bottom: 10px;">⚠️ Error al cargar el mapa</h5>
                    <p style="margin: 5px 0;">No se pudo conectar con Google Maps.</p>
                    <p style="margin: 5px 0; font-size: 12px;">Por favor, recarga la página o contacta al administrador.</p>
                </div>
            `;
        }
    };
    document.head.appendChild(script);
}

// Auto-load the API when this script is executed
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadGoogleMapsAPI);
} else {
    loadGoogleMapsAPI();
}