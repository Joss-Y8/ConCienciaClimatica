
const $ubicacion = document.querySelector('.ubicacion');
// Función para obtener la ubicación
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        $ubicacion.textContent = "Ubicación no disponible.";
    }
}


function showPosition(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;

    // API de geocodificación inversa para obtener el nombre del lugar
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;
    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            const lugar = data.address.city || data.address.town || data.address.state || "Ubicación desconocida";
            $ubicacion.textContent = lugar; // Mostrar solo la ubicación
        })
        .catch(() => {
            $ubicacion.textContent = "No se pudo obtener la ubicación.";
        });
}

// Manejo de errores de Geolocalización
function showError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            $ubicacion.textContent = "Puebla.";
            break;
        case error.POSITION_UNAVAILABLE:
            $ubicacion.textContent = "Puebla.";
            break;
        case error.TIMEOUT:
            $ubicacion.textContent = "Puebla.";
            break;
        default:
            $ubicacion.textContent = "Puebla";
            break;
    }
}

getLocation();
