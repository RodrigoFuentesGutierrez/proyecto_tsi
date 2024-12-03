<?php
// Incluir el archivo de conexión
include '../../php/conexionBD.php';
session_start();

// Comprobar si las variables de sesión están definidas
$fecha = isset($_SESSION['fecha']) ? $_SESSION['fecha'] : null;
$hora_inicio = isset($_SESSION['horai']) ? $_SESSION['horai'] : null;
$hora_termino = isset($_SESSION['horat']) ? $_SESSION['horat'] : null;

// Verificar que todas las variables necesarias estén disponibles
if (!$fecha || !$hora_inicio || !$hora_termino) {
    echo "Faltan datos de sesión (fecha, hora de inicio o hora de término).";
    exit;
}

// Consulta SQL
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
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssss', $hora_inicio, $hora_termino, $fecha, $hora_inicio, $hora_termino, $fecha, $hora_inicio, $hora_termino);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Asistencias por Sala</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="stylesheet" href="../../css/estilo1">
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
        .opciones a {
            color: white;
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
                ['Sala', 'Total Asistencia', 'Porcentaje uso'],
                <?php
                    // Generar los datos para el gráfico
                    while ($row = $result->fetch_assoc()) {
                        echo "['Sala " . $row['sala'] . "', " . $row['total_asistencia'] . ", " . $row['porcentaje_visitas'] . "],";
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

