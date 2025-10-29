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

// Load Google Maps API with the updated API key
function loadGoogleMapsAPI() {
    // Check if API is already loaded
    if (typeof google !== 'undefined' && google.maps) {
        console.log('Google Maps API already loaded');
        return;
    }
    
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAtjzxI9iVgpXTkQ6kFJ33RA5oGngdS6d0&callback=initMap&libraries=places&language=es&region=ES';
    script.async = true;
    script.defer = true;
    script.onerror = function() {
        console.error('Error loading Google Maps API - Please check API key and permissions');
    };
    document.head.appendChild(script);
}

// Auto-load the API when this script is executed
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadGoogleMapsAPI);
} else {
    loadGoogleMapsAPI();
}