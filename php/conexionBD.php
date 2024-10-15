<?php
$host="localhost";
$usuario="root";
$password="";
$bd="conteoasistencia";

$conn=new mysqli($host,$usuario,$password,$bd);

if ($conn->connect_error){
    die("conexion errornea a la base de datos". $conn->connect_error);
} else {
    echo "Conexion exitosa a la base de datos";
}
$conn->close();
?>
