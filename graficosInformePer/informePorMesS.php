<?php
// Incluir el archivo de conexión
include '../php/conexionBD.php';
session_start();

// Obtener los valores del año y mes desde la sesión
$anio = isset($_SESSION['anio']) ? $_SESSION['anio'] : null;
$mes = isset($_SESSION['mes']) ? $_SESSION['mes'] : null;
$sala1 = isset($_SESSION['sala1']) ? $_SESSION['sala1'] : null;

// Verificar que el mes y año estén disponibles
if ($anio === null || $mes === null) {
    die("Por favor seleccione un año y mes.");
}

// Array de los nombres de los meses en español
$meses = array(
    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio",
    7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
);

// Obtener el nombre del mes
$mesNombre = isset($meses[$mes]) ? $meses[$mes] : 'Mes desconocido';
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

        #chartContainer {
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
            <a href="../Expodatosper/generarInformePersonalizadoSalasCerradas.php" title="Ir a Inicio" style=" left:430px; font-size: 25px; position: relative;">
                <i class="fas fa-home"></i>
            </a>
        </div>
    </header>
    <!-- Título con el mes en palabras -->
    <h1>Informe total de asistencias para el mes de <?php echo $mesNombre; ?> del año <?php echo $anio; ?></h1>

    <!-- Dropdown para seleccionar el tipo de gráfico -->
    <select id="chartType" onchange="drawChart()">
        <option value="BarChart">Gráfico de Barras</option>
        <option value="LineChart">Gráfico de Líneas</option>
        <option value="AreaChart">Gráfico de Áreas</option>
        <option value="PieChart">Gráfico Circular</option>
        <option value="ScatterChart">Gráfico de Dispersión</option> <!-- Nuevo gráfico agregado -->
    </select>

    <!-- Div donde se dibujará el gráfico -->
    <div id="chartContainer" style="width: 900px; height: 500px;"></div>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart', 'bar', 'line', 'area', 'scatter']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // Obtener el tipo de gráfico seleccionado
            var chartType = document.getElementById('chartType').value;

            // Crear la estructura de datos para el gráfico
            var data = google.visualization.arrayToDataTable([
                ['Fecha', 'Total Asistencia'],
                <?php
                    // Realizamos la consulta para obtener las asistencias del mes y año seleccionados
                    if ($mes !== null && $anio !== null && $sala1 !== null) {
                        // SQL query to fetch the total attendance per day for the selected month and year
                        $sql = "SELECT 
                                    DATE(fecha) AS fecha,
                                    COUNT(*) AS total_asistencia
                                FROM salas
                                WHERE MONTH(fecha) = ? AND YEAR(fecha) = ? AND sala = ?
                                GROUP BY fecha
                                ORDER BY fecha";

                        // Preparar y ejecutar la consulta
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param('iii', $mes, $anio, $sala1);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // Verificar si se tienen resultados
                        if ($result->num_rows > 0) {
                            // Generar los datos para el gráfico
                            while ($row = $result->fetch_assoc()) {
                                // Mostrar los datos con formato adecuado
                                echo "['" . $row['fecha'] . "', " . $row['total_asistencia'] . "],";
                            }
                        } else {
                            echo "['No hay datos', 0],";
                        }

                        // Cerrar la consulta
                        $stmt->close();
                    }
                ?>
            ]);

            // Opciones comunes para todos los gráficos
            var options = {
                chart: {},
                hAxis: {
                    title: 'Fecha',
                    minValue: 0, // Asegura que las barras comiencen desde 0
                },
                vAxis: {
                    title: 'Total Asistencia',
                },
                height: 500, // Ajusta la altura
            };

            // Seleccionar el gráfico basado en la opción seleccionada
            var chart;
            if (chartType === 'BarChart') {
                chart = new google.visualization.BarChart(document.getElementById('chartContainer'));
            } else if (chartType === 'LineChart') {
                chart = new google.visualization.LineChart(document.getElementById('chartContainer'));
            } else if (chartType === 'AreaChart') {
                chart = new google.visualization.AreaChart(document.getElementById('chartContainer'));
            } else if (chartType === 'PieChart') {
                chart = new google.visualization.PieChart(document.getElementById('chartContainer'));
                options.is3D = true; // Agregar efecto 3D al gráfico circular
            } else if (chartType === 'ScatterChart') {
                chart = new google.visualization.ScatterChart(document.getElementById('chartContainer'));
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
