<?php
// Incluir el archivo de conexión
include '../php/conexionBD.php';
session_start();

// Obtener los valores del año desde la sesión
$anio = isset($_SESSION['anios']) ? $_SESSION['anios'] : null;
$sala2 = isset($_SESSION['sala2']) ? $_SESSION['sala2'] : null;

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <title>Informe por Año</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        header {
            position: relative;
            display: flex;
            align-items: center;
            height: 75px;
            background-color: #5184DC;
            top: -12px;
            width: 1500px;
            left: -3px;
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
            <a href="../Expodatosper/generarInformePersonalizadoSalasCerradas.php" title="Ir a Inicio" style=" left:350px; font-size: 25px; position: relative;">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </header>
    <h1>Informe de asistencias por mes para el año <?php echo $anio; ?></h1>

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
            // Obtener el tipo de gráfico seleccionado
            var chartType = document.getElementById('chartType').value;

            // Crear la estructura de datos para el gráfico
            var data = google.visualization.arrayToDataTable([
                ['Mes', 'Total Asistencia'],
                <?php
                    // Realizamos la consulta para obtener las asistencias por mes para el año seleccionado
                    if ($anio !== null && $sala2 !== null ) {
                        $sql = "SELECT 
                                    MONTH(fecha) AS mes,
                                    COUNT(*) AS total_asistencia
                                FROM salas
                                WHERE YEAR(fecha) = ? AND sala = ?
                                GROUP BY mes
                                ORDER BY mes";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('ii', $anio, $sala2); // Aseguramos que el año sea un entero
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Generar los datos para el gráfico (usamos los nombres de los meses)
                        while ($row = $result->fetch_assoc()) {
                            // Convertir el número del mes (1 a 12) en el nombre del mes
                            $nombre_mes = date("F", mktime(0, 0, 0, $row['mes'], 10)); // Obtiene el nombre del mes
                            echo "['" . $nombre_mes . "', " . $row['total_asistencia'] . "],";
                        }

                        $stmt->close();
                    }
                ?>
            ]);

            // Opciones comunes para todos los gráficos
            var options = {
                hAxis: {
                    title: 'Total Asistencia',
                    minValue: 0, // Asegura que las barras comiencen desde 0
                },
                vAxis: {
                    title: 'Mes',
                },
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
            } else if (chartType === 'PieChart') {
                chart = new google.visualization.PieChart(document.getElementById('barchart'));
                options.is3D = true; // Agregar efecto 3D al gráfico circular
            } else if (chartType === 'ScatterChart') {
                chart = new google.visualization.ScatterChart(document.getElementById('barchart'));
                // Cambiar las opciones para el gráfico de dispersión
                options.hAxis = {
                    title: 'Mes',
                    format: '0' // Formato del eje X
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
