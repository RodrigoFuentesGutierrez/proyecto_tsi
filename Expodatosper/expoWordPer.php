<?php
// Establecer las cabeceras para descargar el archivo Word
header('Content-Type: application/msword');
header('Content-Disposition: attachment; filename=informeAsistencia.doc');

// Incluir la conexión a la base de datos
include '../php/conexionBD.php';

// Recuperar los datos del formulario
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;
$anio = isset($_POST['anio']) ? $_POST['anio'] : null;
$anios = isset($_POST['anios']) ? $_POST['anios'] : null;

echo "<!DOCTYPE html>
<html lang='es'>
<head>
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
if ($fecha) {
    // Asegurarse de que la fecha esté en el formato adecuado (YYYY-MM-DD)
    $fecha = htmlspecialchars($fecha);  // Esto asegura que no haya caracteres especiales en la fecha

    // Consulta SQL utilizando un marcador de posición
    $sql = "SELECT 
            CONCAT(LPAD(HOUR(hora), 2, '0'), ':00 - ', LPAD((HOUR(hora) + 1) % 24, 2, '0'), ':00') AS intervalo,
            COUNT(DISTINCT asistencia) AS total_asistencia,
            DATE(fecha) AS fecha
        FROM asistencia
        WHERE fecha = ? AND HOUR(hora) >= 8 AND HOUR(hora) < 24
        GROUP BY intervalo, DATE(fecha)
        ORDER BY fecha, HOUR(hora)";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro de la fecha
        $stmt->bind_param("s", $fecha);  // 's' indica que el parámetro es una cadena

        // Ejecutar la consulta
        $stmt->execute();
        
        // Obtener el resultado
        $result = $stmt->get_result();
        
        // Generar la tabla para la fecha específica
        echo "<h2>Informe de Asistencia para la fecha: $fecha</h2>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Intervalo</th>
                        <th>Total de Asistencia</th>
                        <th>Fecha</th>
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
                        <td>" . htmlspecialchars($row['fecha']) . "</td>
                      </tr>";
            }
        } else {
            // Si no se encontraron resultados, mostrar un mensaje
            echo "<tr><td colspan='3'>No se encontraron resultados para la fecha: $fecha</td></tr>";
        }
        echo "</tbody></table><br>";
    } else {
        // Si hubo un error al preparar la consulta
        echo "Error al preparar la consulta: " . $conn->error;
    }
} else {
    // Si no se proporcionó una fecha, mostrar un mensaje de error
    echo "Por favor, proporciona una fecha válida.";
}

// Fin de la consulta por fecha

// Si se proporcionó mes y año
if ($mes && $anio) {
    // Consulta SQL para el mes y año
    $sql = "SELECT 
            DATE(fecha) AS fecha, 
            COUNT(*) AS total_asistencia
        FROM asistencia
        WHERE mes = ? AND YEAR(fecha) = ?
        GROUP BY fecha
        ORDER BY fecha";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros de mes y año
        $stmt->bind_param("ss", $mes, $anio);  // 'ss' indica que ambos parámetros son cadenas de texto

        // Ejecutar la consulta
        $stmt->execute();
        
        // Obtener el resultado
        $result = $stmt->get_result();
        
        // Generar la tabla para el mes y año
        echo "<h2>Informe de Asistencia para el mes: $mes de $anio</h2>";
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
            // Si no se encontraron resultados, mostrar un mensaje en la tabla
            echo "<tr><td colspan='2'>No se encontraron resultados para el mes: $mes de $anio</td></tr>";
        }
        echo "</tbody></table><br>";
    } else {
        // Si hubo un error al preparar la consulta
        echo "Error al preparar la consulta: " . $conn->error;
    }
}

// Si se proporcionó año
if ($anios) {
    // Consulta SQL para el año
    $sql = "SELECT 
            YEAR(fecha) AS año, 
            MONTHNAME(fecha) AS mes, 
            SUM(asistencia) AS total_asistencia
        FROM asistencia
        WHERE YEAR(fecha) = ?
        GROUP BY YEAR(fecha), MONTH(fecha)
        ORDER BY MONTH(fecha)";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro de año
        $stmt->bind_param("i", $anios); // 'i' indica que el parámetro es un entero

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

// Cerrar la conexión a la base de datos
$conn->close();

echo "</body></html>";
?>

