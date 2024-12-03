<?php
include '../php/conexionBD.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <title>Informe Básico</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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

        #chart {
            border-radius: 10px;
            background-color: #ffffff;
            margin-bottom: 30px;
        }

        select {
            font-size: 16px;
            padding: 10px;
            margin-top: 20px;
        }
        
        header {
            position: relative;
            display: flex;
            align-items: center; 
            height: 70px; 
            background-color: #5184DC; 
            width: 1500px;
            left: -10px;
        }

        .logo {
            display: flex;
        }

        .logo img {
            height: 50px; 
        }
        .opciones1 a {
            font-size: 18px; 
            color: white; 
            margin: 10px;
            text-decoration: none;
        }
        .opciones1 {
            position: relative;
            left: 1150px;
            top: -30px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo text-center mb-4">
            <img src="../img/logo.png" alt="Logo USM">
        </div>
        <div class="opciones1">
            <a href="../src/menuOpciones.html" title="Ir a Inicio" style="margin-left: 40px; font-size: 25px; position: relative; top:30px;">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </header>
    <h1>Informe total de asistencias por fecha</h1>
    <select id="chartType" onchange="drawChart()">
        <option value="LineChart">Gráfico de Líneas</option>
        <option value="BarChart">Gráfico de Barras</option>
    </select>
    <div id="chart" style="width: 900px; height: 500px;"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar', 'line']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var chartType = document.getElementById('chartType').value;  // Obtener el valor seleccionado

            var data = google.visualization.arrayToDataTable([
                ['Fecha', 'Total Asistencia'],
                <?php
                    // Consultar las asistencias por fecha
                    $sql = "SELECT fecha, SUM(asistencia) AS total_asistencia FROM asistencia GROUP BY fecha";
                    $result = $conn->query($sql); 
                    while ($row = $result->fetch_assoc()) {
                        echo "['" . $row['fecha'] . "', " . $row['total_asistencia'] . "],";
                    }   
                ?>
            ]);
            var options = {
                hAxis: {
                    title: 'Fecha',
                },
                vAxis: {
                    title: 'Total Asistencia',
                },
                lineWidth: 3, // Para gráfico de líneas, opcional para grosor de línea
            };

            var chart;
            if (chartType === 'BarChart') {
                // Si el tipo de gráfico seleccionado es BarChart
                chart = new google.visualization.BarChart(document.getElementById('chart'));
            } else if (chartType === 'LineChart') {
                // Si el tipo de gráfico seleccionado es LineChart
                chart = new google.visualization.LineChart(document.getElementById('chart'));
            }

            // Dibujar el gráfico según el tipo seleccionado
            chart.draw(data, options);
        }
    </script>
</body>
</html>
