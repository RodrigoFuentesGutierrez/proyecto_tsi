<?php
// Conexión a la base de datos
include '../php/conexionBD.php';

session_start();

// Validar las variables del formulario
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;
$anio1 = isset($_POST['anio1']) ? $_POST['anio1'] : null;
$mes1 = isset($_POST['mes1']) ? $_POST['mes1'] : null;
$anio = isset($_POST['anio']) ? $_POST['anio'] : null;
$fechai = isset($_POST['fechai']) ? $_POST['fechai'] : null;
$fechat = isset($_POST['fechat']) ? $_POST['fechat'] : null;
$horat = isset($_POST['horat']) ? $_POST['horat'] : null;
$horai = isset($_POST['horai']) ? $_POST['horai'] : null;


$_SESSION['fecha'] = $fecha;
$_SESSION['mes'] = $mes;
$_SESSION['anio'] = $anio;
$_SESSION['mes1'] = $mes1;
$_SESSION['anio1'] = $anio1;
$_SESSION['fechai'] = $fechai;
$_SESSION['fechat'] = $fechat;
$_SESSION['horat'] = $horat;
$_SESSION['horai'] = $horai;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <title>Informes de Asistencia</title>
    <link rel="stylesheet" href="../css/estilo1.css">
    <style>
        .table-container {
            background-color: #3A4B90;
        }
        
    </style>
</head>
<body>
<header>
        <div class='logo'>
            <img src='../img/logo.png' alt='Logo'>
        </div>
        <div class='opciones'>
            <nav class='navegacion'>
                <ul>
                     <li>
                        <a href='../porsentajeUso/gaficosExtaditicos/informepormes.php' title='Exportar a Word'>
                            Porcentaje Por mes
                        </a>
                    <li>
                    <li>
                        <a href='../porsentajeUso/gaficosExtaditicos/informeporsemanauso.php' title='informe por semana'>
                                Porcentaje por semana
                        </a>
                    <li>
                     <li>
                        <a href='../porsentajeUso/gaficosExtaditicos/informerangohorasdia.php' title='informerangohorasdia'>
                            Porcentaje de rango de horas por dia
                        </a>
                    <li>
                        <a href='expoExtadisticasWord.php' title='Exportar a Word'>
                            Exportacion a Word
                        </a>
                    </li>

                    <!-- Enlace a ExpoExcel -->
                    <li>
                        <a href='expoExtadisticasExcel.php' title='Exportar a Excel'>
                            Exportacion a Excel
                        </a>
                    </li>
                    <!-- Ícono de la casa primero -->
                    <li>
                    <a href="Acordeon.php" title="Ir a Inicio" style="margin-left: 25px; font-size: 25px; position: relative; top:0px;">
                            <i class='fas fa-home' style='font-size: 20px;'></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
<?php
// ** Informe por hora **
if ($fecha && $horai && $horat) {
    // Consulta SQL para obtener el total de asistencia y porcentaje de visitas por sala
    $sql = "
    SELECT 
        sala,  -- Número de la sala
        ? AS hora_inicio,  -- Hora de inicio
        ? AS hora_termino,  -- Hora de término
        COUNT(*) AS total_asistencia,
        (COUNT(*) / 
            (SELECT COUNT(*) 
            FROM salas 
            WHERE fecha = ? 
            AND hora BETWEEN ? AND ? 
            AND sala BETWEEN 1 AND 9)
        ) * 100 AS porcentaje_visitas  -- Porcentaje de visitas
    FROM salas
    WHERE fecha = ?  -- Filtra por la fecha seleccionada
    AND hora BETWEEN ? AND ?  -- Filtra por el rango de horas completo (con minutos)
    AND sala BETWEEN 1 AND 9  -- Filtra por las salas del 1 al 9
    GROUP BY sala  -- Agrupa por sala
    ORDER BY total_asistencia DESC  -- Ordena de mayor a menor por el total de visitas
    ";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros a la consulta preparada
        $stmt->bind_param('ssssssss', $horai, $horat, $fecha, $horai, $horat, $fecha, $horai, $horat);
        // Ejecutar la consulta
        $stmt->execute();
        // Obtener los resultados
        $result = $stmt->get_result();
        
        // Verificar si se obtuvieron resultados
        if ($result->num_rows > 0) {
            // Mostrar los resultados en una tabla HTML
            echo "<h1>Informe de asistencia por rango de hora para la fecha $fecha</h1>";
            echo "<div class='table-container'>";
            echo "<table class='visitas-table'>
                    <tr>
                        <th>Sala</th>
                        <th>Hora de Inicio</th>
                        <th>Hora de Término</th>
                        <th>Total de Asistencia</th>
                        <th>Porcentaje de Asistencia</th>
                    </tr>";

            // Recorrer y mostrar cada fila de resultados
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['sala']}</td>
                        <td>{$row['hora_inicio']}</td>
                        <td>{$row['hora_termino']}</td>
                        <td>{$row['total_asistencia']}</td>
                        <td>" . number_format($row['porcentaje_visitas'], 2) . "%</td>
                    </tr>";
            }

            // Cerrar la tabla
            echo "</table></div>";
        } else {
            // Mostrar mensaje si no se encontraron visitas
            echo "<h3 class='no-result'>No se encontraron visitas en este rango horario para la fecha seleccionada.</h3>";
        }

        // Cerrar la declaración preparada
        $stmt->close();
    }
}

// ** Informe por rango de fechas **
if ($anio1 && $mes1 && $fechai && $fechat) {
    // Consulta SQL para obtener el porcentaje de asistencia por sala
    $sql = "
        SELECT 
            sala, 
            DATE(fecha) AS dia, 
            COUNT(*) AS total_asistencia,
            CONCAT(ROUND(
                (COUNT(*) * 100.0 / 
                    (SELECT COUNT(*) 
                    FROM salas 
                    WHERE YEAR(fecha) = ? 
                    AND MONTH(fecha) = ? 
                    AND fecha BETWEEN ? AND ?)
                ), 2), '%') AS porcentaje_asistencia,
            (COUNT(*) * 100.0 / 
                (SELECT COUNT(*) 
                FROM salas 
                WHERE YEAR(fecha) = ? 
                AND MONTH(fecha) = ? 
                AND fecha BETWEEN ? AND ?)
            ) AS porcentaje_numerico
        FROM salas
        WHERE 
            YEAR(fecha) = ? 
            AND MONTH(fecha) = ? 
            AND fecha BETWEEN ? AND ? 
        GROUP BY sala, dia
        ORDER BY porcentaje_numerico DESC;
    ";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros a la consulta preparada
        $stmt->bind_param("ssssssssssss", $anio1, $mes1, $fechai, $fechat, $anio1, $mes1, $fechai, $fechat, $anio1, $mes1, $fechai, $fechat);
        // Ejecutar la consulta
        $stmt->execute();
        // Obtener los resultados
        $result = $stmt->get_result();

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Mostrar los resultados
            echo "<h1>Informe de asistencia para el rango de fechas $fechai al $fechat del mes $mes del año $anio</h1>";
            echo "<div class='table-container'>";
            echo "<table class='visitas-table'>
                    <tr>
                        <th>Sala</th>
                        <th>Total_Asistencia</th>
                        <th>Porcentaje de Asistencia</th>
                    </tr>";

            // Recorrer los resultados y mostrarlos en la tabla
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['sala'] . "</td>
                        <td>" . $row['total_asistencia'] . "</td>
                        <td>" . $row['porcentaje_asistencia'] . "</td>
                    </tr>";
            }

            echo "</table></div>";
        } else {
            echo "<h3 class='no-result'>No se encontraron resultados para el rango de fechas seleccionado.</h3>";
        }

        // Cerrar la declaración preparada
        $stmt->close();
    }
}

// ** Informe mensual por mes y año **
if ($mes && $anio) {
    // Consulta SQL para obtener la sala con mayor asistencia por día del mes
    $sql = "
    WITH total_asistentes_mes AS (
        SELECT COUNT(*) AS total_asistentes  
        FROM salas
        WHERE YEAR(fecha) = ? AND MONTH(fecha) = ? 
    ),
    ranked_salas AS (
        SELECT
            DATE(fecha) AS fecha,
            sala,
            COUNT(*) AS cantidad_asistentes,  
            ROW_NUMBER() OVER (PARTITION BY DATE(fecha) ORDER BY COUNT(*) DESC) AS ranking
        FROM salas
        WHERE YEAR(fecha) = ? AND MONTH(fecha) = ?  
        GROUP BY DATE(fecha), sala  
    )
    SELECT
        rs.fecha,
        rs.sala,
        rs.cantidad_asistentes,
        ROUND((rs.cantidad_asistentes / tam.total_asistentes) * 100, 2) AS porcentaje_asistencia  
    FROM ranked_salas rs
    JOIN total_asistentes_mes tam  
    WHERE rs.ranking = 1  
    ORDER BY porcentaje_asistencia DESC, rs.fecha ASC;
    ";

    // Preparar y ejecutar la consulta SQL
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("iiii", $anio, $mes, $anio, $mes);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si se obtuvieron resultados
        if ($result->num_rows > 0) {
            echo "<h1>Informe de asistencia por sala con mayor asistencia para el mes  del año $anio</h1>";
            echo "<div class='table-container'>";
            echo "<table>
                    <tr>
                        <th>Fecha</th>
                        <th>Sala</th>
                        <th>Cantidad de Asistentes</th>
                        <th>Porcentaje de Asistencia</th>
                    </tr>";

            // Recorrer y mostrar los resultados
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['fecha']}</td>
                        <td>{$row['sala']}</td>
                        <td>{$row['cantidad_asistentes']}</td>
                        <td>" . number_format($row['porcentaje_asistencia'], 2) . "%</td>
                    </tr>";
            }
            echo "</table></div>";
        } else {
            echo "<h3>No se encontraron visitas para el mes seleccionado.</h3>";
        }
        $stmt->close();
    }
}
?>

</body>
</html>
