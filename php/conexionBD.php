<?php
// Detalles de conexión
$hostname = "localhost";
$database = "conteoasistencia";
$username = "root";
$password = "";

// Crear la conexión
$conn = new mysqli($hostname, $username, $password, $database);

// Verificar la conexión
//if ($conn->connect_error) {
    //die("Error al conectar con la base de datos: " . $conn->connect_error);
//} else {
    //echo "Conexión exitosa a la base de datos.";
//}

// Cerrar la conexión (si es necesario en algún punto)
// $conn->close()