<?php
// Incluir el archivo de conexión
include '../php/conexionBD.php';
session_start();

// Obtener los valores de fecha desde la sesión
$fechai = isset($_SESSION['fechai']) ? $_SESSION['fechai'] : null;
$fechat = isset($_SESSION['fechat']) ? $_SESSION['fechat'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe Básico</title>
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

        #barchart {
            border-radius: 10px;
            background-color: #ffffff;
        }
        header {
            position: relative;
            display: flex;
            align-items: center; 
            height: 70px; 
            background-color: #5184DC; 
            width: 1544px;
            left: -4px;
            top: 0px;
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
            left: 750px;
        }

    </style>
</head>
<body>
<header>
        <div class="logo">
            <img src="../img/logo.png" alt="Logo USM">
        </div>
        <div class="opciones1">
            <a href="../Expodatosper/generarInformePersonalizadoSalasAbiertas.php" title="Ir a Inicio" style=" left:480px; font-size: 25px; position: relative;">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </header>
    <h1>Informe total de asistencias por rango de fechas: <br> <?php echo $fechai . ' a ' . $fechat; ?></h1>

    <!-- Dropdown para seleccionar el tipo de gráfico -->
    <select id="chartType" onchange="drawChart()">
        <option value="BarChart">Gráfico de Barras</option>
        <option value="LineChart">Gráfico de Líneas</option>
        <option value="AreaChart">Gráfico de Áreas</option>
    </select>

    <!-- Div donde se dibujará el gráfico -->
    <div id="barchart" style="width: 900px; height: 500px;"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar', 'line', 'area']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
    // Obtener el tipo de gráfico seleccionado
    var chartType = document.getElementById('chartType').value;

    // Crear la estructura de datos para el gráfico
    var data = google.visualization.arrayToDataTable([
        ['Fecha + Intervalo', 'Total Asistencia'],
        <?php
            // Realizamos la consulta para obtener las asistencias del rango de fechas seleccionado
            if ($fechai !== null && $fechat !== null) {
                $sql = "WITH Intervalos AS (
                    SELECT DATE(fecha) AS fecha,
                           CONCAT(LPAD(HOUR(hora), 2, '0'), ':00 - ', LPAD((HOUR(hora) + 1) % 24, 2, '0'), ':00') AS intervalo,
                           COUNT(*) AS total_asistencia
                    FROM asistencia
                    WHERE fecha BETWEEN ? AND ?
                      AND HOUR(hora) BETWEEN 8 AND 23
                    GROUP BY fecha, intervalo
                )
                SELECT CONCAT(fecha, ' ', intervalo) AS fecha_intervalo, total_asistencia
                FROM Intervalos i
                WHERE total_asistencia = (SELECT MAX(total_asistencia) FROM Intervalos WHERE fecha = i.fecha)
                ORDER BY fecha;";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $fechai, $fechat);
                $stmt->execute();
                $result = $stmt->get_result();

                $first = true;
                while ($row = $result->fetch_assoc()) {
                    if (!$first) {
                        echo ",";
                    }
                    // Pasamos 'fecha_intervalo' y 'total_asistencia'
                    echo "['" . $row['fecha_intervalo'] . "', " . $row['total_asistencia'] . "]";
                    $first = false;
                }

                $stmt->close();
            }
        ?>
    ]);

    // Configuración de opciones
    var options = {
        title: 'Total de Asistencias por Intervalo',
        hAxis: { title: 'Fecha + Intervalo'},
        vAxis: { title: 'Asistencias' },
        height: 500, // Ajusta la altura
    };

    // Seleccionar el gráfico basado en la opción seleccionada
    var chart;
    if (chartType === 'BarChart') {
        chart = new google.visualization.BarChart(document.getElementById('barchart'));
    } else if (chartType === 'LineChart') {
        chart = new google.visualization.LineChart(document.getElementById('barchart'));
    } else if (chartType === 'AreaChart') {
        chart = new google.visualization.AreaChart(document.getElementById('barchart'));
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
