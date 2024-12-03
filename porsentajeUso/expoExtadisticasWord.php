<?php
// Establecer las cabeceras para descargar el archivo Excel
header('Content-Type: application/msword');
header('Content-Disposition: attachment; filename=informeAsistencia.doc');

// Incluir la conexión a la base de datos
include '../php/conexionBD.php';

// Iniciar la sesión para obtener las variables de sesión
session_start();
$fecha = isset($_SESSION['fecha']) ? $_SESSION['fecha'] : null;
$mes = isset($_SESSION['mes']) ? $_SESSION['mes'] : null;
$año = isset($_SESSION['año']) ? $_SESSION['año'] : null;
$mes1 = isset($_SESSION['mes1']) ? $_SESSION['mes1'] : null;
$año1 = isset($_SESSION['anio1']) ? $_SESSION['anio1'] : null;
$fechat = isset($_SESSION['fechat']) ? $_SESSION['fechat'] : null;
$fechai = isset($_SESSION['fechai']) ? $_SESSION['fechai'] : null;
$hora_termino = isset($_SESSION['horat']) ? $_SESSION['horat'] : null;
$hora_inicio = isset($_SESSION['horai']) ? $_SESSION['horai'] : null;

// Nombres de los meses
$meses = array(
    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio",
    7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
);
$mesNombre = isset($meses[$mes]) ? $meses[$mes] : 'Mes desconocido';

// Generar HTML con estilo CSS para las tablas
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
            font-size: 18px;
        }
    </style>
</head>
<body>";

// **1. Informe por Rango de Hora (Fecha y Hora específica)**
if ($fecha && $hora_inicio && $hora_termino) {
    // Consulta SQL para obtener el total de visitas y porcentaje de visitas por sala
    $sql = "
    SELECT 
        sala,  
        ? AS hora_inicio,  
        ? AS hora_termino,  
        COUNT(*) AS total_asistencia,
        (COUNT(*) / 
            (SELECT COUNT(*) 
            FROM salas 
            WHERE fecha = ? 
            AND hora BETWEEN ? AND ? 
            AND sala BETWEEN 1 AND 9)
        ) * 100 AS porcentaje_visitas  
    FROM salas
    WHERE fecha = ? 
    AND hora BETWEEN ? AND ? 
    AND sala BETWEEN 1 AND 9  
    GROUP BY sala  
    ORDER BY total_asistencia DESC  
    ";

    // Preparar y ejecutar la consulta SQL
    if ($stmt = $conn->prepare($sql)) {
        // Enlazar parámetros
        $stmt->bind_param('ssssssss', $hora_inicio, $hora_termino, $fecha, $hora_inicio, $hora_termino, $fecha, $hora_inicio, $hora_termino);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si se obtuvieron resultados
        if ($result->num_rows > 0) {
            echo "<h1>Informe de asistencia por rango de hora para la fecha $fecha</h1>";
            echo "<table>
                    <tr>
                        <th>Sala</th>
                        <th>Hora de Inicio</th>
                        <th>Hora de Término</th>
                        <th>Total de Asistencia</th>
                        <th>Porcentaje de Asistencia</th>
                    </tr>";

            // Recorrer y mostrar los resultados
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['sala']}</td>
                        <td>{$row['hora_inicio']}</td>
                        <td>{$row['hora_termino']}</td>
                        <td>{$row['total_asistencia']}</td>
                        <td>" . number_format($row['porcentaje_visitas'], 2) . "%</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<h3>No se encontraron visitas en este rango horario para la fecha seleccionada.</h3>";
        }
        $stmt->close();
    }
}

// **2. Informe por Rango de Fechas (Mes y Año)**
if ($año1 && $mes1 && $fechai && $fechat) {
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
        $stmt->bind_param("ssssssssssss", $año1, $mes1, $fechai, $fechat, $año1, $mes1, $fechai, $fechat, $año1, $mes1, $fechai, $fechat);
        // Ejecutar la consulta
        $stmt->execute();
        // Obtener los resultados
        $result = $stmt->get_result();

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Mostrar los resultados
            echo "<h1>Informe de asistencia para el rango de fechas $fechai al $fechat del mes $mesNombre del año $año</h1>";
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
            echo "<h3>No se encontraron resultados para el rango de fechas seleccionado.</h3>";
        }

        // Cerrar la declaración preparada
        $stmt->close();
    }
}

// **3. Informe de Asistencia por Sala con Mayor Asistencia del Mes**
if ($mes && $año) {
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
        $stmt->bind_param("iiii", $año, $mes, $año, $mes);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si se obtuvieron resultados
        if ($result->num_rows > 0) {
            echo "<h1>Informe de asistencia por sala con mayor asistencia para el mes $mesNombre del año $año</h1>";
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
            echo "</table>";
        } else {
            echo "<h3>No se encontraron visitas para el mes seleccionado.</h3>";
        }
        $stmt->close();
    }
}

// Cerrar la conexión a la base de datos
$conn->close();

echo "</body></html>";
?>