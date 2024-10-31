<?php
session_start();
include("conexionBD.php");

// Obtener los datos del formulario
$correo = $_POST['correo'];
$password = $_POST['password'];

$sql_check = "SELECT * FROM usuario WHERE correo ='$correo' AND contraseña='$password'";
$result_check = $conn->query($sql_check);

// Validar el formato del correo electrónico
if (preg_match("/^[a-zA-Z]+\.[a-zA-Z]+@usm\.cl$/", $correo)) {
   
} elseif (strpos($correo, '@usm.cl') === false) {
    // Mensaje de error si no hay '@usm.cl'
    echo "<script>alert('El correo debe incluir el dominio @usm.cl.');</script>";
    echo "<script>window.location.href='../src/login.html';</script>";
    exit;
}
if ($result_check->num_rows > 0) {
        // Agregar alerta de inicio de sesión correcto
        echo "<script>alert('Inicio de sesión correcto. Bienvenido!');</script>";
        echo "<script>window.location.href='../src/menuOpciones.html';</script>";
        exit;
} else {
        echo "<script>alert('Correo electrónico o contraseña incorrectos. Por favor, inténtalo de nuevo.');</script>";
        echo "<script>window.location.href='../src/login.html';</script>";
        exit;
}
