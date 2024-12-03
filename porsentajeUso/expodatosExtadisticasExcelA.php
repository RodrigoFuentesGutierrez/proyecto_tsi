<?php
// Establecer las cabeceras para descargar el archivo Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename=informeAsistencia.xls');

// Incluir la conexión a la base de datos
include '../php/conexionBD.php';

// Iniciar la sesión para obtener las variables de sesión
session_start();
$fechai = isset($_SESSION['fechai']) ? $_SESSION['fechai'] : null;
$fechat = isset($_SESSION['fechat']) ? $_SESSION['fechat'] : null;
$horai = isset($_SESSION['horai']) ? $_SESSION['horai'] : null;
$horat = isset($_SESSION['horat']) ? $_SESSION['horat'] : null;
$anio = isset($_SESSION['anio']) ? $_SESSION['anio'] : null;
$fechai1 = isset($_SESSION['fechai1']) ? $_SESSION['fechai1'] : null;
$fechat1 = isset($_SESSION['fechat1']) ? $_SESSION['fechat1'] : null;

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

// **1. Informe por Rango de Fecha y Hora**
if ($fechai && $fechat && $horai && $horat) {
    $sql = "
        SELECT DATE(fecha) AS dia, '$horai' AS hora_inicio, '$horat' AS hora_termino,
        COUNT(*) AS total_asistentes,
        (COUNT(*) / total_general) * 100 AS porcentaje_uso
        FROM asistencia,
        (SELECT COUNT(*) AS total_general 
        FROM asistencia 
        WHERE fecha BETWEEN '$fechai $horai' AND '$fechat $horat') AS total
        WHERE fecha BETWEEN '$fechai $horai' AND '$fechat $horat'
        GROUP BY dia
        ORDER BY total_asistentes DESC;
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<h2>Informe de Asistencia por Rango de Fecha y Hora</h2>";
        echo "<table>
                <tr>
                    <th>Día</th>
                    <th>Hora de Inicio</th>
                    <th>Hora de Término</th>
                    <th>Total de Asistentes</th>
                    <th>Porcentaje de Uso</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['dia']}</td>
                    <td>{$row['hora_inicio']}</td>
                    <td>{$row['hora_termino']}</td>
                    <td>{$row['total_asistentes']}</td>
                    <td>" . number_format($row['porcentaje_uso'], 2) . "%</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<h3>No se encontraron datos para el rango seleccionado.</h3>";
    }
}

// **2. Informe por Mes y Año**
if ($anio) {
    $sql = "
        SELECT MONTH(fecha) AS mes, COUNT(*) AS total_registros,
        CONCAT(ROUND((COUNT(*) * 100.0 / 
            (SELECT COUNT(*) 
            FROM asistencia 
            WHERE YEAR(fecha) = $anio)), 2), '%') AS porcentaje_uso
        FROM asistencia
        WHERE YEAR(fecha) = $anio 
        GROUP BY MONTH(fecha)
        ORDER BY mes;
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<h2>Informe de Asistencia por Mes y Año</h2>";
        echo "<table>
                <tr>
                    <th>Mes</th>
                    <th>Total Registros</th>
                    <th>Porcentaje de Uso</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['mes']}</td>
                    <td>{$row['total_registros']}</td>
                    <td>{$row['porcentaje_uso']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<h3>No se encontraron datos para el año seleccionado.</h3>";
    }
}

// **3. Informe por Rango de Fechas**
if ($fechai1 && $fechat1) {
    $sql = "
        SELECT DATE(fecha) AS dia, COUNT(*) AS total_asistentes,
        CONCAT(ROUND((COUNT(*) * 100.0 / 
            (SELECT COUNT(*) 
            FROM asistencia 
            WHERE fecha BETWEEN '$fechai1' AND '$fechat1')), 2), '%') AS porcentaje_asistencia
        FROM asistencia
        WHERE fecha BETWEEN '$fechai1' AND '$fechat1'
        GROUP BY dia
        ORDER BY total_asistentes DESC;
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo "<h2>Informe de Asistencia por Rango de Fechas</h2>";
        echo "<table>
                <tr>
                    <th>Día</th>
                    <th>Total de Asistentes</th>
                    <th>Porcentaje de Asistencia</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['dia']}</td>
                    <td>{$row['total_asistentes']}</td>
                    <td>{$row['porcentaje_asistencia']}</td>
                </tr>";
        }
        echo "</table>";
    } else {
        echo "<h3>No se encontraron datos para el rango de fechas seleccionado.</h3>";
    }
}

// Cerrar la conexión
$conn->close();

echo "</body></html>";
?>
