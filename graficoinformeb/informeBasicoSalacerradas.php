<?php
// Incluir el archivo de conexión
include '../php/conexionBD.php';
session_start();

// Obtener la sala desde la sesión
$sala = isset($_SESSION['sala']) ? $_SESSION['sala'] : null;
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
            margin-bottom: 20px;
        }

        #barchart {
            border-radius: 10px;
            background-color: #ffffff;
            margin-bottom: 20px;
        }

        select {
            font-size: 16px;
            padding: 10px;
            margin-top: 20px;
        }
        #chartType{
            position: relative;
            top: -30px;
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
            font-size: 27px; 
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
    <h1>Informe total de asistencias por fecha - Sala <?php echo $sala; ?></h1>
    <select id="chartType" onchange="drawChart()">
        <option value="BarChart">Gráfico de Barras</option>
        <option value="LineChart">Gráfico de Líneas</option>
    </select>
    <div id="barchart" style="width: 900px; height: 500px; top: -15px; position: relative"></div>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var chartType = document.getElementById('chartType').value;  // Obtener el valor seleccionado en el <select>

            var data = google.visualization.arrayToDataTable([
                ['Fecha', 'Total Asistencia'],
                <?php
                    // Realizamos la consulta para obtener las asistencias de la sala seleccionada
                    if ($sala !== null) {
                        $sql = "SELECT fecha, SUM(asistencia) AS total_asistencia FROM salas WHERE sala = ? GROUP BY fecha";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('i', $sala);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Generar los datos para el gráfico
                        while ($row = $result->fetch_assoc()) {
                            echo "['" . $row['fecha'] . "', " . $row['total_asistencia'] . "],";
                        }

                        $stmt->close();
                    }
                ?>
            ]);

            var options = {
                chart: {},
                bars: 'horizontal', // Barra horizontal
                hAxis: {
                    title: 'Total Asistencia',
                    minValue: 0, // Asegura que las barras comiencen desde 0
                },
                vAxis: {
                    title: 'Fecha',
                },
                height: 500, // Ajusta la altura
            };

            var chart;
            if (chartType === 'BarChart') {
                chart = new google.visualization.BarChart(document.getElementById('barchart'));
            } else if (chartType === 'LineChart') {
                chart = new google.visualization.LineChart(document.getElementById('barchart'));
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
