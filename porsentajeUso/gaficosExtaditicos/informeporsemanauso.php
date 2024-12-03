<?php
// Incluir el archivo de conexión
include '../../php/conexionBD.php';
session_start();

// Recuperar los valores de las variables de sesión o de las entradas (fecha, año, mes, rango de fechas)
$fecha_inicio = isset($_SESSION['fechai']) ? $_SESSION['fechai'] : null;
$fecha_termino = isset($_SESSION['fechat']) ? $_SESSION['fechat'] : null;
$anio1 = isset($_SESSION['anio1']) ? $_SESSION['anio1'] : null;
$mes1 = isset($_SESSION['mes1']) ? $_SESSION['mes1'] : null;

if (!$fecha_inicio  || !$fecha_termino  || !$anio1 || !$mes1) {
    echo "Faltan datos de sesión (fecha inicio, fecha de termino, año y mes).";
    exit;
}

// Consulta SQL
if ($anio1 && $mes1 && $fecha_inicio && $fecha_termino) {
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
        $stmt->bind_param("ssssssssssss", $anio1, $mes1, $fecha_inicio, $fecha_termino, $anio1, $mes1, $fecha_inicio, $fecha_termino, $anio1, $mes1, $fecha_inicio, $fecha_termino);
        // Ejecutar la consulta
        $stmt->execute();
        // Obtener los resultados
        $result = $stmt->get_result();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Asistencias por Sala</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        header {
            position: relative;
            display: flex;
            align-items: center; 
            height: 70px; 
            background-color: #5184DC; 
            width: 1539px;
            top: -9px;
            left:5px;
        }

        .logo {
            display: flex;
        }

        .logo img {
            position: relative;
            height: 70px;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            font-size: 36px;
            color: #5e5e5e;
            text-align: center;
            margin-bottom: 30px;
        }

        select {
            font-size: 16px;
            padding: 8px;
            margin-bottom: 20px;
        }

        #barchart {
            border-radius: 10px;
            background-color: #ffffff;
        }
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="../../img/logo.png" alt="Logo"> 
    </div>
    <div class="opciones">
    <a href="../Acordeon.php" title="Ir a Inicio" style="margin-left: 1115px; font-size: 25px; position: relative; top: 0px;">
            <i class="fas fa-home"></i>
        </a>
    </div>
    </header>
    <h1>Informe de Asistencias por Sala</h1>

    <!-- Dropdown para seleccionar el tipo de gráfico -->
    <select id="chartType" onchange="drawChart()">
        <option value="BarChart">Gráfico de Barras</option>
        <option value="LineChart">Gráfico de Líneas</option>
        <option value="AreaChart">Gráfico de Áreas</option>
        <option value="PieChart">Gráfico Circular</option>
        <option value="ScatterChart">Gráfico de Dispersión</option>
    </select>

    <!-- Div donde se dibujará el gráfico -->
    <div id="barchart" style="width: 900px; height: 500px;"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar', 'line', 'area', 'pie', 'scatter']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var chartType = document.getElementById('chartType').value;

            // Crear la estructura de datos para el gráfico
            var data = google.visualization.arrayToDataTable([
                ['Sala', 'Total Asistencia', 'Porcentaje de Asistencia'],
                <?php
                    // Generar los datos para el gráfico
                    while ($row = $result->fetch_assoc()) {
                        echo "['Sala " . $row['sala'] . " - " . $row['dia'] . "', " . $row['total_asistencia'] . ", " . $row['porcentaje_numerico'] . "],";
                    }
                ?>
            ]);

            // Opciones comunes para todos los gráficos
            var options = {
                hAxis: {
                    title: 'Total Asistencia',
                    minValue: 0
                },
                vAxis: {
                    title: 'Sala'
                },
                height: 500
            };

            // Seleccionar el gráfico basado en la opción seleccionada
            var chart;
            if (chartType === 'BarChart') {
                chart = new google.visualization.BarChart(document.getElementById('barchart'));
            } else if (chartType === 'LineChart') {
                chart = new google.visualization.LineChart(document.getElementById('barchart'));
            } else if (chartType === 'AreaChart') {
                chart = new google.visualization.AreaChart(document.getElementById('barchart'));
            } else if (chartType === 'PieChart') {
                chart = new google.visualization.PieChart(document.getElementById('barchart'));
                options.is3D = true; // Agregar efecto 3D al gráfico circular
            } else if (chartType === 'ScatterChart') {
                chart = new google.visualization.ScatterChart(document.getElementById('barchart'));
                options.hAxis = {
                    title: 'Sala',
                    format: '0'
                };
                options.vAxis = {
                    title: 'Total Asistencia',
                    format: '0'
                };
            }

            // Dibujar el gráfico
            chart.draw(data, options);
        }
    </script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>

