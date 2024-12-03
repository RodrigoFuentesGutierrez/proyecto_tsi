<?php
// Incluir el archivo de conexión
include '../php/conexionBD.php';
session_start();

// Obtener los valores de fecha desde la sesión
$fecha = isset($_SESSION['fecha']) ? $_SESSION['fecha'] : null;
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
            <a href="../Expodatosper/generarInformePersonalizadoSalasAbiertas.php" title="Ir a Inicio" style=" left:425px; font-size: 25px; position: relative;">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </header>
    <h1>Informe total de asistencias por fecha para la fecha <?php echo htmlspecialchars($fecha); ?></h1>

    <!-- Dropdown para seleccionar el tipo de gráfico -->
    <select id="chartType" onchange="drawChart()">5
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
                ['Fecha', 'Total Asistencia'],
                <?php
                    // Realizamos la consulta para obtener las asistencias de la fecha seleccionada
                    if ($fecha !== null) {
                        // Consulta SQL para obtener los datos agrupados por intervalo de hora
                        $sql = "SELECT
                                CASE 
                                    WHEN HOUR(hora) >= 6 AND HOUR(hora) < 12 THEN CONCAT(DATE_FORMAT(hora, '%l %p'), ' a las ', DATE_FORMAT(DATE_ADD(hora, INTERVAL 1 HOUR), '%l %p'))  -- Mañana
                                    WHEN HOUR(hora) >= 12 AND HOUR(hora) < 18 THEN CONCAT(DATE_FORMAT(hora, '%l %p'), ' a las ', DATE_FORMAT(DATE_ADD(hora, INTERVAL 1 HOUR), '%l %p'))  -- Tarde
                                    WHEN HOUR(hora) >= 18 AND HOUR(hora) < 24 THEN CONCAT(DATE_FORMAT(hora, '%l %p'), ' a las ', DATE_FORMAT(DATE_ADD(hora, INTERVAL 1 HOUR), '%l %p'))  -- Noche
                                    ELSE CONCAT(DATE_FORMAT(hora, '%l %p'), ' a las ', DATE_FORMAT(DATE_ADD(hora, INTERVAL 1 HOUR), '%l %p'))  -- Madrugada
                                END AS intervalo_hora,
                                SUM(asistencia) AS total  
                            FROM
                                asistencia
                            WHERE
                                fecha = ? 
                            GROUP BY
                                HOUR(hora) 
                            ORDER BY
                                HOUR(hora)";

                        // Preparar y ejecutar la consulta
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('s', $fecha);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Verificamos si hay resultados y generamos los datos para el gráfico
                        $hasData = false;
                        while ($row = $result->fetch_assoc()) {
                            $hasData = true;
                            // Imprimir cada intervalo de hora y su total de asistencia
                            echo "['" . addslashes($row['intervalo_hora']) . "', " . (int)$row['total'] . "],";
                        }

                        // Si no hay datos, mostramos un mensaje para indicar que no hay información disponible
                        if (!$hasData) {
                            echo "['No hay datos disponibles', 0],";
                        }

                        // Cerrar la consulta
                        $stmt->close();
                    }
                ?>
            ]);

            // Opciones comunes para todos los gráficos
            var options = {
                hAxis: {
                    title: 'Intervalo de Hora',
                },
                vAxis: {
                    title: 'Total Asistencia',
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
                    title: 'Intervalo de Hora',
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
