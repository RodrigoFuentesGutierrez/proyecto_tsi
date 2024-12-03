<?php
// Incluir el archivo de conexión
include '../php/conexionBD.php';

// Cambiar el idioma a español para la conexión
$conn->query("SET lc_time_names = 'es_ES'");

// Definir el valor de asistencia que se quiere insertar
$asistencia = 1; // Puedes cambiar este valor según la lógica de tu aplicación
$sala=1;
// Consulta para insertar los datos con el mes en español
$sql = "INSERT INTO salas (asistencia, fecha, hora, mes, sala) 
        VALUES ($asistencia, CURDATE(), CURTIME(), DATE_FORMAT(NOW(), '%M'),$sala)";
    
// Ejecutar la consulta y comprobar si se realizó correctamente
if ($conn->query($sql) === TRUE) {
    $registro_completado = true; // Indica que el registro se completó
} else {
    echo "Error al insertar: " . $conn->error; // Mostrar error si hay un problema
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Asistencia</title>
    <link rel="stylesheet" href="../css/msj.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../img/logo.png" alt="Logo USM">
        </div>
    </header>

    <dialog open id="mensaje">
        <h1>Registro de asistencia completado para la sala 1</h1>
        <p>- Puede cerrar este apartado </p>
    </dialog>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
?>





