<?php
// Incluir el archivo de conexión
include '../../php/conexionBD.php';
session_start();

// Recuperar los valores de las variables de sesión o de las entradas (fecha, año, mes, rango de fechas)
$anio = isset($_SESSION['anio']) ? $_SESSION['anio'] : null;
$mes = isset($_SESSION['mes']) ? $_SESSION['mes'] : null;

if (!$anio || !$mes) {
    echo "Faltan datos de sesión (del año y el mes).";
    exit;
}

// Consulta SQL
if ($anio && $mes) {
    $sql = "
    WITH total_asistentes_mes AS (
        SELECT COUNT(*) AS total_asistentes  
        FROM salas
        WHERE YEAR(fecha) = ? AND MONTH(fecha) = ? 
    ),
    ranked_salas AS (
        SELECT
            DATE(fecha) AS fecha,
            sala,
            COUNT(*) AS cantidad_asistentes,  
            ROW_NUMBER() OVER (PARTITION BY DATE(fecha) ORDER BY COUNT(*) DESC) AS ranking
        FROM salas
        WHERE YEAR(fecha) = ? AND MONTH(fecha) = ?  
        GROUP BY DATE(fecha), sala  
    )
    SELECT
        rs.fecha,
        rs.sala,
        rs.cantidad_asistentes,
        ROUND((rs.cantidad_asistentes / tam.total_asistentes) * 100, 2) AS porcentaje_asistencia  
    FROM ranked_salas rs
    JOIN total_asistentes_mes tam  
    WHERE rs.ranking = 1  
    ORDER BY porcentaje_asistencia DESC, rs.fecha ASC;
    ";

    // Preparar y ejecutar la consulta SQL
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros a la consulta preparada
        $stmt->bind_param("iiii", $anio, $mes, $anio, $mes);
        // Ejecutar la consulta
        $stmt->execute();
        // Obtener los resultados
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
    <title>Informe de Asistencias por Sala</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
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
            left: -4px;
            top: -23px;
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
    <div class="logo ">
    <img src="../../img/logo.png" alt="Logo USM"> 
    </div>
    <div class="opciones1">
        <a href="../Acordeon.php" title="Ir a Inicio" style="margin-left: 400px; font-size: 25px; position: relative; top: 0px;">
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
                ['Fecha', 'Cantidad Asistentes', 'Porcentaje de Asistencia'],
                <?php
                    // Generar los datos para el gráfico con sala incluida en el tooltip
                    while ($row = $result->fetch_assoc()) {
                        echo "['" . $row['fecha'] . " (sala:" . $row['sala'] . ")', " . $row['cantidad_asistentes'] . ", " . $row['porcentaje_asistencia'] . "],";
                    }
                ?>
            ]);

            // Opciones comunes para todos los gráficos
            var options = {
                hAxis: {
                    title: 'Cantidad de Asistentes',
                    minValue: 0
                },
                vAxis: {
                    title: 'Fecha'
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
                    title: 'Fecha',
                    format: '0'
                };
                options.vAxis = {
                    title: 'Cantidad de Asistentes',
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
