<?php
// Incluir el archivo de conexión
include '../../php/conexionBD.php';
session_start();

// Recuperar las fechas de inicio y fin desde las variables de sesión o desde el formulario
$fechai1 = isset($_SESSION['fechai1']) ? $_SESSION['fechai1'] : null;
$fechat1 = isset($_SESSION['fechat1']) ? $_SESSION['fechat1'] : null;

if (!$fechai1 || !$fechat1) {
    echo "Faltan datos de sesión (fecha inicio y fecha termino).";
    exit;
}

if ($fechai1 && $fechat1) {
    // Consultar los datos de asistencia entre las fechas proporcionadas
    $sql = "
    SELECT 
        DATE(fecha) AS dia, 
        COUNT(*) AS total_asistentes,
        CONCAT(ROUND((COUNT(*) * 100.0 / 
            (SELECT COUNT(*) FROM asistencia WHERE fecha BETWEEN ? AND ?)), 2), '%') AS porcentaje_asistencia
    FROM asistencia
    WHERE fecha BETWEEN ? AND ?
    GROUP BY dia
    ORDER BY total_asistentes DESC;
    ";

    if ($stmt = $conn->prepare($sql)) {
        // Vincular las fechas a los parámetros
        $stmt->bind_param("ssss", $fechai1, $fechat1, $fechai1, $fechat1);
        $stmt->execute();
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
    <title>Informe de Asistencias por Día</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <link rel="stylesheet" href="../../css/estilo1.css">
    <style>
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
        #chart_div {
            border-radius: 10px;
            background-color: #ffffff;
            width: 900px;
            height: 500px;
        }
    </style>
</head>
<body>
    <header>
    <div class="logo">
        <img src="../../img/logo.png" alt="Logo"> 
    </div>
    <div class="opciones">
        <a href="../gerneraInformeExtadisticoSalasAbiertas.php" title="Ir a Inicio" style="margin-left: 5px; font-size: 25px; position: relative; top: 0px;">
            <i class="fas fa-home"></i>
        </a>
    </div>
    </header>

    <h1>Informe de Asistencias por Día</h1>

    <!-- Dropdown para seleccionar el tipo de gráfico -->
    <select id="chartType" onchange="drawChart()">
        <option value="BarChart">Gráfico de Barras</option>
        <option value="LineChart">Gráfico de Líneas</option>
        <option value="PieChart">Gráfico Circular</option>
    </select>

    <!-- Div donde se dibujará el gráfico -->
    <div id="chart_div"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar', 'line', 'pie']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var chartType = document.getElementById('chartType').value;

            var data = google.visualization.arrayToDataTable([
                ['Día', 'Total Asistentes', 'Porcentaje de Asistencia'],
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "['" . $row['dia'] . "', " . $row['total_asistentes'] . ", " . str_replace('%', '', $row['porcentaje_asistencia']) . "],";
                    }
                } else {
                    echo "['Sin Datos', 0, 0]";
                }
                ?>
            ]);

            var options = {
                hAxis: { title: 'Día' },
                vAxis: { title: 'Total Asistentes' },
                height: 500
            };

            var chart;
            if (chartType === 'BarChart') {
                chart = new google.visualization.BarChart(document.getElementById('chart_div'));
            } else if (chartType === 'LineChart') {
                chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            } else if (chartType === 'PieChart') {
                chart = new google.visualization.PieChart(document.getElementById('chart_div'));
                options.is3D = true;
            }

            chart.draw(data, options);
        }
    </script>
</body>
</html>

<?php
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>
