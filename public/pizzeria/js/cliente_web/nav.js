$(document).on("click", "#perfil", function() {
    const clientemall = JSON.parse(localStorage.getItem('clientemall'));
    if (clientemall) {
        $("#modal-perfil").modal('show');
    } else {
        $("#modal-inicio-sesion").modal('show');
        // alerta("Inicia sesión", "Debes iniciar sesión para ver tu perfil", 2000);
    }
});

$(document).on("click", "#mis-pedidos", function() {
    const clientemall = JSON.parse(localStorage.getItem('clientemall'));
    if (clientemall) {
        
        // Crear un enlace temporal
        const enlaceTemporal = document.createElement('a');
        enlaceTemporal.href = rutaLocal + "mis-pedidos/" + clientemall.id_cliente;
        enlaceTemporal.click();
    } else {
        alerta("Inicia sesión", "Debes iniciar sesión para ver tus pedidos", 2000);
    }
});

$(document).on("click", "#cerrar-sesion", function() {
    localStorage.removeItem('clientemall');
    const enlaceTemporal = document.createElement('a');
    enlaceTemporal.href = rutaLocal;
    enlaceTemporal.click();
});