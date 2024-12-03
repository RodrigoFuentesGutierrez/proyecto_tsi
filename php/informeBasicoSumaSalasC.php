<?php
// Incluir el archivo de conexión
include 'conexionBD.php';
session_start();

// Obtener el valor de 'sala' desde POST (si existe)
$sala = isset($_POST['sala']) ? $_POST['sala'] : null;
$_SESSION['sala'] = $sala; // Guardamos la sala seleccionada en la sesión

// Si no se ha proporcionado un valor para la sala, no se realiza la consulta
if ($sala !== null) {
    // Consulta SQL con marcador de posición para la sala
    $sql = "SELECT fecha, SUM(asistencia) AS total_asistencia, sala FROM salas WHERE sala = ? GROUP BY fecha";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Verificar si la consulta se preparó correctamente
    if ($stmt === false) {
        die('Error al preparar la consulta: ' . $conn->error);
    }

    // Vincular el parámetro (en este caso, la variable $sala que es un entero)
    $stmt->bind_param('i', $sala); // 'i' es el tipo de datos para enteros

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener los resultados
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia</title>
    <link rel="stylesheet" href="../css/tabla.css">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
</head>
<body>
<header>
    <div class="logo">
        <img src="../img/logo.png" alt="Logo"> 
    </div>
    <div class="opciones">
        <a href="../src/menuOpciones.htmll" title="Ir a Inicio" style="left: 500px; font-size: 25px; position: relative;">
            <i class="fas fa-home"></i>
        </a>
        <a href="../graficoinformeb/informeBasicoSalacerradas.php">graficoSalasCerradas</a>
        <a href="../ExpDatos/expoWordBasico.php">Exportar a Word</a>
        <a href="../ExpDatos/expoExcelBasico.php">Exportar a Excel</a>

    </div>
</header>

<h2>Registro de Asistencia</h2>
<div class="tabla-container">
<div class="container1">
    <form action="informeBasicoSumaSalasC.php" method="POST">
                <select name="sala" id="sala">
                    <option value="" <?php if ($sala=="selet_sala") echo 'selected'; ?>>select_sala</option>
                    <option value="1" <?php if ($sala == 1) echo 'selected'; ?>>Sala 1</option>
                    <option value="2" <?php if ($sala == 2) echo 'selected'; ?>>Sala 2</option>
                    <option value="3" <?php if ($sala == 3) echo 'selected'; ?>>Sala 3</option>
                    <option value="4" <?php if ($sala == 4) echo 'selected'; ?>>Sala 4</option>
                    <option value="5" <?php if ($sala == 5) echo 'selected'; ?>>Sala 5</option>
                    <option value="6" <?php if ($sala == 6) echo 'selected'; ?>>Sala 6</option>
                    <option value="7" <?php if ($sala == 7) echo 'selected'; ?>>Sala 7</option>
                    <option value="8" <?php if ($sala == 8) echo 'selected'; ?>>Sala 8</option>
                    <option value="9" <?php if ($sala == 9) echo 'selected'; ?>>Sala 9</option>
                </select>
                <button class="btn" type="submit">Generar Informe</button>
    </form>
</div>

<div class='tabla-container'>
<div class="container">
    <table>
        <thead>
            <tr>
                <th>Asistencia</th>
                <th>Fecha</th>
                <th>Sala</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Mostrar los datos solo si se ha enviado un valor para la sala
        if (isset($result) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['total_asistencia'] . "</td>
                        <td>" . $row['fecha'] . "</td>
                        <td>" . $row['sala'] . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No se encontraron resultados.</td></tr>";
        }
        echo "</table></div>";
        ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
// Cerrar la conexión si se ha abierto la consulta
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>

