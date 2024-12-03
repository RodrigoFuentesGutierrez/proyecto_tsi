function validarContraseñas() {
    // Obtener los valores de los campos de contraseña
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirmarPassword").value;

    // Validar que las contraseñas coincidan
    if (password !== confirmPassword) {
        alert("Las contraseñas no coinciden. Intenta nuevamente.");
        return false;  // Detener la acción si las contraseñas no coinciden
    }

    // Validar que la contraseña tenga al menos 6 caracteres
    if (password.length < 6) {
        alert("La contraseña debe tener al menos 6 caracteres.");
        return false;  // Detener la acción si la contraseña es demasiado corta
    }

    // Si pasa todas las validaciones, enviar el formulario manualmente
    document.getElementById("cambioFormulario").submit();
}