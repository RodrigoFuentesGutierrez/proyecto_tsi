<?php
// Incluir el archivo de conexión
include '../../php/conexionBD.php';
session_start();

// Recuperar el valor del año desde las variables de sesión
$anio = isset($_SESSION['anio']) ? $_SESSION['anio'] : null;

if (!$anio) {
    echo "Faltan el dato de sesión (del año).";
    exit;
}

if ($anio) {
    $sql = "
    SELECT 
        MONTH(fecha) AS mes, 
        COUNT(*) AS total_registros,
        ROUND((COUNT(*) * 100.0 / 
            (SELECT COUNT(*) FROM asistencia WHERE YEAR(fecha) = ?)), 2) AS porcentaje_uso
    FROM asistencia
    WHERE YEAR(fecha) = ?
    GROUP BY MONTH(fecha)
    ORDER BY mes;
    ";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $anio, $anio);
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
    <title>Informe de Asistencias por Año</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
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
        header {
            position: relative;
            display: flex;
            align-items: center; 
            height: 70px; 
            background-color: #5184DC;
            left: -4px;
            top: -20px;
            width: 1500px;
        }

        .logo {
            display: flex;
        }

        .logo img {
            height: 50px; /* Ajusta la altura del logo */
        }
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
    </style>
</head>
<body>
<header>
    <div class="logo">
        <img src="../../img/logo.png" alt="Logo USM">
    </div>
    <div class="opciones1">
        <a href="../gerneraInformeExtadisticoSalasAbiertas.php" title="Ir a Inicio" style="margin-left: 450px; font-size: 25px; position: relative; top: 4px;">
            <i class="fas fa-home"></i>
        </a>
    </div>
</header>

    <h1>Informe de Asistencias por Mes</h1>

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
                ['Mes', 'Total Registros', 'Porcentaje de Uso'],
                <?php
                if ($result && $result->num_rows > 0) {
                    $meses_es = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                    while ($row = $result->fetch_assoc()) {
                        $mes_nombre = $meses_es[$row['mes'] - 1];
                        echo "['" . $mes_nombre . "', " . $row['total_registros'] . ", " . $row['porcentaje_uso'] . "],";
                    }
                } else {
                    echo "['Sin Datos', 0, 0]";
                }
                ?>
            ]);

            var options = {
                hAxis: { title: 'Mes' },
                vAxis: { title: 'Total Registros' },
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


