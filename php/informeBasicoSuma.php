<?php
// Incluir el archivo de conexión
include 'conexionBD.php';

// Consulta para obtener los datos
$sql = "SELECT fecha, SUM(asistencia) AS total_asistencia FROM asistencia GROUP BY fecha";
$result = $conn->query($sql);
// Crear la tabla HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia</title>
    <link rel="stylesheet" href="../css/tabla.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
<header>
    <div class="logo">
        <img src="../img/logo.png" alt="Logo"> 
    </div>
    <div class="opciones1">
        <a href="../graficoinformeb/informeBasicoSalaAbierta.php">graficoSalaAbierta</a>
        <a href="../ExpDatos/expoWordBasico.php">Exportar a Word</a>
        <a href="../ExpDatos/expoExcelBasico.php">Exportar a Excel</a>
        <a href="../src/menuOpciones.html" title="Ir a Inicio" style="margin-left: 15px; font-size: 20px;">
            <i class="fas fa-home"></i>
        </a>
    </div>
</header>
<h2>Registro de Asistencia</h2>
<div class='tabla-container'>
<div class="container">
    <table>
        <thead>
            <tr>
                <th>Asistencia</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Mostrar los datos
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['total_asistencia'] . "</td>
                        <td>" . $row['fecha'] . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No se encontraron resultados.</td></tr>";
        }
        echo "</table></div>";
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php
// Cerrar la conexión
$conn->close();
?>
