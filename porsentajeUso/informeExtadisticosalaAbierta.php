<?php
// Conexión a la base de datos
include '../php/conexionBD.php';

session_start();

// Capturando los valores enviados por el formulario
$horai = isset($_POST['horai']) ? $_POST['horai'] : null;
$horat = isset($_POST['horat']) ? $_POST['horat'] : null;
$fechai = isset($_POST['fechai']) ? $_POST['fechai'] : null;
$fechat = isset($_POST['fechat']) ? $_POST['fechat'] : null;
$fechai1 = isset($_POST['fechai1']) ? $_POST['fechai1'] : null;
$fechat1 = isset($_POST['fechat1']) ? $_POST['fechat1'] : null;
$anio = isset($_POST['anio']) ? $_POST['anio'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;

$_SESSION['horai'] = $horai;
$_SESSION['horat'] = $horat;
$_SESSION['fechai'] = $fechai;
$_SESSION['fechat'] = $fechat;
$_SESSION['fechai1'] = $fechai1;
$_SESSION['fechat1'] = $fechat1;
$_SESSION['anio'] = $anio;
$_SESSION['mes'] = $mes;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Estadístico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <link rel="stylesheet" href="../css/estilo1.css">
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
                        <a href='../porsentajeUso/gaficosExtaditicos/informeporsemana.php' title='Exportar a Word'>
                            Porcentaje Por semana
                        </a>
                    <li>
                    <li>
                        <a href='../porsentajeUso/gaficosExtaditicos/informeporaño.php' title='informe por semana'>
                                Porcentaje por año 
                        </a>
                    <li>
                     <li>
                        <a href='../porsentajeUso/gaficosExtaditicos/informesemanarangohoras.php' title='informerangohorasdia'>
                            Porcentajes de las horas
                        </a>
                    <li>
                        <a href='expodatosExtadisticasWordA.php' title='Exportar a Word'>
                            Exportacion a Word
                        </a>
                    </li>

                    <!-- Enlace a ExpoExcel -->
                    <li>
                        <a href='expodatosExtadisticasExcelA.php' title='Exportar a Excel'>
                            Exportacion a Excel
                        </a>
                    </li>
                    <!-- Ícono de la casa primero -->
                    <li>
                        <a href='gerneraInformeExtadisticoSalasAbiertas.php' title='Ir a Inicio'>
                            <i class='fas fa-home' style='font-size: 20px;'></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
        <?php
        // ** Informe por rango de hora **
        if ($horai && $horat && $fechai && $fechat) {
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

            if ($stmt = $conn->prepare($sql)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    echo "<h3>Informe por Hora</h3>";
                    echo "<table class='table'>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora de Inicio</th>
                                <th>Hora de Término</th>
                                <th>Total Asistentes</th>
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
                    echo "<h3>No se encontraron resultados para el rango de horas seleccionado.</h3>";
                }
                $stmt->close();
            }
        }

        // ** Informe por año y mes **
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

            if ($stmt = $conn->prepare($sql)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    echo "<h3>Informe por Mes y Año</h3>";
                    echo "<table class='table'>
                            <tr>
                                <th>Mes</th>
                                <th>Total de Registros</th>
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
                    echo "<h3>No se encontraron resultados para el mes seleccionado.</h3>";
                }
                $stmt->close();
            }
        }

        // ** Informe por rango de fechas **
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

            if ($stmt = $conn->prepare($sql)) {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    echo "<h3>Informe por Rango de Fechas</h3>";
                    echo "<table class='table'>
                            <tr>
                                <th>Fecha</th>
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
                    echo "<h3>No se encontraron resultados para el rango de fechas seleccionado.</h3>";
                }
                $stmt->close();
            }
        }
        ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
