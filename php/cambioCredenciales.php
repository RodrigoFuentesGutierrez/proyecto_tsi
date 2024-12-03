<?php

// Incluye el archivo que contiene la conexión a la base de datos.
include("conexionBD.php");

// Obtener los datos enviados desde el formulario a través del método POST.
$correo = $_POST['correo'];    // Almacena el correo recibido desde el formulario.
$password = $_POST['password']; // Almacena la contraseña recibida desde el formulario.

// Validar el formato del correo
if (preg_match("/^[a-zA-Z]+\.[a-zA-Z]+@usm\.cl$/", $correo)) {
    // El correo cumple con el formato correcto (nombre.apellido@usm.cl)
} elseif (strpos($correo, '@usm.cl') === false) {
    // Mensaje de error si no hay '@usm.cl'
    echo "<script>alert('El correo debe incluir el dominio @usm.cl.');</script>";
    echo "<script>window.location.href='../src/cambioContraseña.html';</script>";
    exit;
//} else {
    // Si el formato del correo no es correcto, muestra un mensaje de error.
    //echo "<script>alert('El correo debe seguir el formato nombre.apellido@usm.cl.');</script>";
    //echo "<script>window.location.href='../src/login.html';</script>";
    //exit;
}

// Prepara una consulta SQL para verificar si el correo ya existe en la base de datos.
$sql_check = "SELECT * FROM usuario WHERE correo = ?";  // Consulta SQL con un marcador de posición (?).
$stmt_check = $conn->prepare($sql_check);  // Prepara la consulta SQL para ejecución.
$stmt_check->bind_param("s", $correo);  // Asocia el valor del correo al marcador de posición en la consulta (el "s" indica que es una cadena).
$stmt_check->execute();  // Ejecuta la consulta SQL.

$result_check = $stmt_check->get_result();  // Obtiene el resultado de la consulta ejecutada (la fila correspondiente al correo).

// Verifica si se encontró al menos un usuario con el correo proporcionado.
if ($result_check->num_rows > 0) {
    // Si el correo existe, se procede a actualizar la contraseña.
    
    $sql_update = "UPDATE usuario SET contrasena = ? WHERE correo = ?";  // Consulta SQL para actualizar la contraseña de un usuario específico.
    $stmt_update = $conn->prepare($sql_update);  // Prepara la consulta SQL de actualización.
    $stmt_update->bind_param("ss", $password, $correo);  // Asocia los valores de la nueva contraseña y el correo a los marcadores de posición (ambos son cadenas).
    $stmt_update->execute();  // Ejecuta la consulta SQL de actualización.
    // Verifica si la actualización fue exitosa.
    if ($stmt_update->affected_rows > 0) {
        // Genera un script de JavaScript para mostrar la alerta de éxito.
        echo "<script>alert('Contraseña actualizada correctamente.'); window.location.href='../src/login.html';</script>";  // Redirige a una página de destino tras mostrar la alerta.
    }
} else {
    // Si no se encontró el correo en la base de datos, muestra un mensaje indicando que el correo no está registrado.
    echo "<script>alert('Error intentalo nuevamente.'); window.location.href='../src/cambioContraseña.html';</script>";
}

// Cierra los recursos utilizados por las consultas.
$stmt_check->close();  // Cierra la declaración de la consulta de verificación.
$stmt_update->close();  // Cierra la declaración de la consulta de actualización.
$conn->close();  // Cierra la conexión con la base de datos.
?>
