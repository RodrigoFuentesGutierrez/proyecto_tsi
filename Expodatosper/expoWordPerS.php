<?php
// Establecer las cabeceras para descargar el archivo Word
header('Content-Type: application/msword');
header('Content-Disposition: attachment; filename=informeAsistencia.doc');

include '../php/conexionBD.php';

// Iniciar la sesión para obtener las variables de sesión
session_start();

// Recuperar los datos del formulario desde la sesión
$fecha = isset($_SESSION['fecha']) ? $_SESSION['fecha'] : null;
$mes = isset($_SESSION['mes']) ? $_SESSION['mes'] : null;
$anio = isset($_SESSION['anio']) ? $_SESSION['anio'] : null;
$anios = isset($_SESSION['anios']) ? $_SESSION['anios'] : null;
$sala1 = isset($_SESSION['sala1']) ? $_SESSION['sala1'] : null;
$sala2 = isset($_SESSION['sala2']) ? $_SESSION['sala2'] : null;
$sala3 = isset($_SESSION['sala3']) ? $_SESSION['sala3'] : null;
$sala4 = isset($_SESSION['sala4']) ? $_SESSION['sala4'] : null;
$fechai = isset($_SESSION['fechai']) ? $_SESSION['fechai'] : null;
$fechat = isset($_SESSION['fechat']) ? $_SESSION['fechat'] : null;
echo "<!DOCTYPE html>
<html lang='es'>
<head
    <meta charset='UTF-8'>
    <title>Informe de Asistencia</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #1E90FF;
            color: white;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>";



// Verificar si se ha proporcionado una fecha específica
if ($fecha && $sala3) {
    // Asegurarse de que la fecha esté en el formato adecuado (YYYY-MM-DD)
    $fecha = htmlspecialchars($fecha);  // Esto asegura que no haya caracteres especiales en la fecha
    // Consulta SQL utilizando un marcador de posición
    $sql = "SELECT 
            CONCAT(LPAD(HOUR(hora), 2, '0'), ':00 - ', LPAD((HOUR(hora) + 1) % 24, 2, '0'), ':00') AS intervalo,
            COUNT(*) AS total_asistencia,  -- Cambié COUNT(DISTINCT asistencia) por COUNT(*)
            DATE(fecha) AS fecha
            FROM salas
            WHERE fecha = ? AND HOUR(hora) >= 8 AND HOUR(hora) < 24 AND sala = ?
            GROUP BY intervalo, DATE(fecha)
            ORDER BY fecha, HOUR(hora)";


    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro de la fecha
        $stmt->bind_param("ss", $fecha, $sala3);  // 's' indica que el parámetro es una cadena

        // Ejecutar la consulta
        $stmt->execute();
        
        // Obtener el resultado
        $result = $stmt->get_result();
        
        // Generar la tabla para la fecha específica
        echo "<h2>Informe de Asistencia para la fecha: $fecha</h2>";
        echo "<div class='tabla-container'>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Intervalo</th>
                        <th>Total de Asistencia</th>
                    </tr>
                </thead>
                <tbody>";

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Recorrer los resultados y mostrarlos en la tabla
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['intervalo']) . "</td>
                        <td>" . htmlspecialchars($row['total_asistencia']) . "</td>
                      </tr>";
            }
        } else {
            // Si no se encontraron resultados, mostrar un mensaje
            echo "<tr><td colspan='3'>No se encontraron resultados para la fecha: $fecha</td></tr>";
        }
        echo "</table></div>";
    } else {
        // Si hubo un error al preparar la consulta
        echo "Error al preparar la consulta: " . $conn->error;
    }
} else {
    // Si no se proporcionó una fecha, mostrar un mensaje de error
    //Secho "Por favor, proporciona una fecha válida.";
}

// Fin de la consulta por fecha

// Si se proporcionó mes y año
if ($mes && $anio && $sala1) {
    // Consulta SQL para el mes y año
    $sql = "SELECT 
            DATE(fecha) AS fecha,
            COUNT(*) AS total_asistencia
            FROM salas
            WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? AND sala = ?
            GROUP BY fecha
            ORDER BY fecha;";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros de mes y año
        $stmt->bind_param("sss", $mes,  $anio, $sala1);  // 'ss' indica que ambos parámetros son cadenas de texto

        // Ejecutar la consulta
        $stmt->execute();
        
        // Obtener el resultado
        $result = $stmt->get_result();
        
        // Generar la tabla para el mes y año
        echo "<h2>Informe de Asistencia para el mes: $mes del $anio</h2>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Total de Asistencia</th>
                    </tr>
                </thead>
                <tbody>";

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Recorrer los resultados y mostrarlos en la tabla
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['fecha']) . "</td>
                        <td>" . htmlspecialchars($row['total_asistencia']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No se encontraron resultados para el mes: $mes de $anio</td></tr>";
        }
    }
}

// Si se proporcionó año
if ($anios && $sala2) {
    // Consulta SQL para el año
    $sql = "SELECT 
            YEAR(fecha) AS año, 
            MONTHNAME(fecha) AS mes, 
            COUNT(*) AS total_asistencia
        FROM salas
        WHERE YEAR(fecha) = ? AND sala = ?
        GROUP BY YEAR(fecha), MONTH(fecha)
        ORDER BY MONTH(fecha)";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro de año
        $stmt->bind_param("ii", $anios, $sala2); // 'i' indica que el parámetro es un entero

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->get_result();

        // Generar la tabla para el año
        echo "<h2>Informe de Asistencia para el año: $anios</h2>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Total de Asistencia</th>
                    </tr>
                </thead>
                <tbody>";

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['mes']) . "</td>
                        <td>" . htmlspecialchars($row['total_asistencia']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No se encontraron resultados para el año: $anios</td></tr>";
        }
        echo "</tbody></table><br>";
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }
}
if ($fechai && $fechat && $sala4) {
    $sql = "WITH Intervalos AS (
        SELECT DATE(fecha) AS fecha,
               CONCAT(LPAD(HOUR(hora), 2, '0'), ':00 - ', LPAD((HOUR(hora) + 1) % 24, 2, '0'), ':00') AS intervalo,
               COUNT(*) AS total_asistencia
        FROM salas
        WHERE fecha BETWEEN ? AND ? AND sala = ?
          AND HOUR(hora) BETWEEN 8 AND 23
        GROUP BY fecha, intervalo
    )
    SELECT fecha, intervalo, total_asistencia
    FROM Intervalos i
    WHERE total_asistencia = (SELECT MAX(total_asistencia) FROM Intervalos WHERE fecha = i.fecha)
    ORDER BY fecha;";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros de fecha de inicio y fecha de fin
        $stmt->bind_param("sss", $fechai, $fechat, $sala4); // 'ss' indica que ambos parámetros son cadenas (strings)
    
        // Ejecutar la consulta
        $stmt->execute();
    
        // Obtener el resultado
        $result = $stmt->get_result();
    
        // Generar la tabla para el informe de asistencia
        echo "<h2>Informe de Asistencia para la sala $sala4 para el período: $fechai a $fechat </h2>";
        echo "<table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Intervalo</th>
                    <th>Total de Asistencia</th>
                </tr>
            </thead>
            <tbody>";
        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['fecha']) . "</td>
                        <td>" . htmlspecialchars($row['intervalo']) . "</td>
                        <td>" . htmlspecialchars($row['total_asistencia']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No se encontraron resultados para el período: $fechai a $fechat</td></tr>";
        }
    
        echo "</tbody></table>";
    
        // Cerrar el statement
        $stmt->close();
    }
}
// Cerrar la conexión a la base de datos
$conn->close();

echo "</body></html>";
?>