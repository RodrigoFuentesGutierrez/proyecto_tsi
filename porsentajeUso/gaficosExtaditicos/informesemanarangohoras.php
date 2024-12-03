<?php
// Incluir el archivo de conexión
include '../../php/conexionBD.php';
session_start();

// Recuperar los valores de las variables de sesión o de las entradas (fecha y hora)
$horai = isset($_SESSION['horai']) ? $_SESSION['horai'] : null;
$horat = isset($_SESSION['horat']) ? $_SESSION['horat'] : null;
$fechai = isset($_SESSION['fechai']) ? $_SESSION['fechai'] : null;
$fechat = isset($_SESSION['fechat']) ? $_SESSION['fechat'] : null;

if (!$fechai || !$fechat || !$horai || !$horat) {
    echo "Faltan datos de sesión (fecha, hora de inicio o hora de término).";
    exit;
}

if ($horai && $horat && $fechai && $fechat) {
    $sql = "
    SELECT 
        DATE(fecha) AS dia, 
        ? AS hora_inicio, 
        ? AS hora_termino,
        COUNT(*) AS total_asistentes,
        (COUNT(*) / total.total_general) * 100 AS porcentaje_uso
    FROM asistencia, 
    (SELECT COUNT(*) AS total_general 
     FROM asistencia 
     WHERE fecha BETWEEN CONCAT(?, ' ', ?) AND CONCAT(?, ' ', ?)) AS total
    WHERE fecha BETWEEN CONCAT(?, ' ', ?) AND CONCAT(?, ' ', ?)
    GROUP BY dia
    ORDER BY total_asistentes DESC;
    ";

    // Preparar y ejecutar la consulta SQL
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "ssssssssss",
            $horai, $horat,
            $fechai, $horai, $fechat, $horat,
            $fechai, $horai, $fechat, $horat
        );
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
    <title>Informe de Asistencias</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        .opciones1 a {
            font-size: 18px; 
            color: white; 
            margin: 10px;
            text-decoration: none;
            }
            .opciones1 {
                position: relative;
                left: 750px;
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
            width: 900px;
            height: 500px;
        }
        header {
            position: relative;
            display: flex;
            align-items: center; 
            height: 70px; 
            background-color: #5184DC;
            left: -10px;
            top: -8px;
            width: 1530px;
        }

        .logo {
            display: flex;
        }

        .logo img {
            height: 50px; /* Ajusta la altura del logo */
        }
    </style>
</head>
<body>
<header>
    <div class="logo ">
        <img src="../../img/logo.png" alt="Logo USM">
    </div>
    <div class="opciones1">
        <a href="../gerneraInformeExtadisticoSalasAbiertas.php" title="Ir a Inicio" style="margin-left: 470px; font-size: 25px; position: relative; top: 0px;">
            <i class="fas fa-home"></i>
        </a>
    </div>
</header>

    <h1>Informe de Asistencias</h1>

    <!-- Dropdown para seleccionar el tipo de gráfico -->
    <select id="chartType" onchange="drawChart()">
        <option value="BarChart">Gráfico de Barras</option>
        <option value="LineChart">Gráfico de Líneas</option>
        <option value="AreaChart">Gráfico de Áreas</option>
        <option value="PieChart">Gráfico Circular</option>
        <option value="ScatterChart">Gráfico de Dispersión</option>
    </select>

    <!-- Div donde se dibujará el gráfico -->
    <div id="barchart"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar', 'line', 'area', 'pie', 'scatter']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var chartType = document.getElementById('chartType').value;

            // Crear la estructura de datos para el gráfico
            var data = google.visualization.arrayToDataTable([
                ['Día', 'Total Asistentes', 'Porcentaje de Uso'],
                <?php
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo "['" . $row['dia'] . "', " . $row['total_asistentes'] . ", " . $row['porcentaje_uso'] . "],";
                    }
                }
                ?>
            ]);

            // Opciones comunes para todos los gráficos
            var options = {
                hAxis: { title: 'Total Asistentes', minValue: 0 },
                vAxis: { title: 'Día' },
                height: 500
            };

            var chart;
            if (chartType === 'BarChart') {
                chart = new google.visualization.BarChart(document.getElementById('barchart'));
            } else if (chartType === 'LineChart') {
                chart = new google.visualization.LineChart(document.getElementById('barchart'));
            } else if (chartType === 'AreaChart') {
                chart = new google.visualization.AreaChart(document.getElementById('barchart'));
            } else if (chartType === 'PieChart') {
                chart = new google.visualization.PieChart(document.getElementById('barchart'));
                options.is3D = true; // Gráfico circular con efecto 3D
            } else if (chartType === 'ScatterChart') {
                chart = new google.visualization.ScatterChart(document.getElementById('barchart'));
            }

            chart.draw(data, options);
        }
    </script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
