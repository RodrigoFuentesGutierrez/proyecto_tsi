<?php
// Incluir la conexión a la base de datos
include '../php/conexionBD.php';

session_start();

// Recuperar los datos del formulario
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
$mes = isset($_POST['mes']) ? $_POST['mes'] : null;
$anio = isset($_POST['anio']) ? $_POST['anio'] : null;
$anios = isset($_POST['anios']) ? $_POST['anios'] : null;
$fechai = isset($_POST['fechai']) ? $_POST['fechai'] : null;
$fechat = isset($_POST['fechat']) ? $_POST['fechat'] : null; 


// Guardar los filtros en sesión
$_SESSION['fecha'] = $fecha;
$_SESSION['mes'] = $mes;
$_SESSION['anio'] = $anio;
$_SESSION['anios'] = $anios;
$_SESSION['fechai'] = $fechai;
$_SESSION['fechat'] = $fechat;

$meses = array(
    1 => "Enero", 2 => "Febrero", 3 => "Marzo", 4 => "Abril", 5 => "Mayo", 6 => "Junio",
    7 => "Julio", 8 => "Agosto", 9 => "Septiembre", 10 => "Octubre", 11 => "Noviembre", 12 => "Diciembre"
);

$mesNombre = isset($meses[$mes]) ? $meses[$mes] : 'Mes desconocido';

// Iniciar el documento HTML para la generación del archivo Excel
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Informe de Asistencia</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'> <!-- Agregado Font Awesome -->
    <link rel='stylesheet' href='../css/estilo1.css'>
    <style>
        /* Estilo para que el enlace parezca un botón */
        .btn-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff; /* Color de fondo */
            color: white; /* Color del texto */
            text-align: center;
            text-decoration: none; /* Eliminar subrayado */
            border-radius: 5px; /* Bordes redondeados */
            font-size: 16px;
            cursor: pointer; /* Aparece como puntero cuando se pasa el mouse */
            transition: background-color 0.3s ease; /* Transición suave al pasar el ratón */
        }

        .btn-link:hover {
            background-color: #0056b3; /* Color de fondo cuando el ratón pasa por encima */
        }
        .tabla-container {
            background-color: 	#3A4B90; /* Un color suave para el fondo */
        }
        
    </style>
</head>
<body>";
echo "<header>
        <div class='logo'>
            <img src='../img/logo.png' alt='Logo'>
        </div>
        <div class='opciones'>
            <nav class='navegacion'>
                <ul>
                    <li>
                        <a href='../graficosInformePer/informeIntervaloMes.php' title='Exportar a Word'>
                                informePorIntervaloMes
                        </a>
                    <li>
                     <li>
                        <a href='../graficosInformePer/informePorAño.php' title='Exportar a Word'>
                            informePorAño
                        </a>
                    <li>
                    <li>
                        <a href='../graficosInformePer/informePorDia.php' title='Exportar a Word'>
                                informePorDia
                        </a>
                    <li>
                     <li>
                        <a href='../graficosInformePer/informePorMes.php' title='Exportar a Word'>
                            informePorMes
                        </a>
                    <li>
                        <a href='expoWordPer.php' title='Exportar a Word'>
                            ExpoWord
                        </a>
                    </li>

                    <!-- Enlace a ExpoExcel -->
                    <li>
                        <a href='expoExcelPer.php' title='Exportar a Excel'>
                            ExpoExcel
                        </a>
                    </li>
                    <!-- Ícono de la casa primero -->
                    <li>
                        <a href='generarInformePersonalizadoSalasAbiertas.php' title='Ir a Inicio'>
                            <i class='fas fa-home' style='font-size: 20px;'></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>";
// Verificar si se ha proporcionado una fecha específica
if ($fecha) {
    // Asegurarse de que la fecha esté en el formato adecuado (YYYY-MM-DD)
    $fecha = htmlspecialchars($fecha);  // Esto asegura que no haya caracteres especiales en la fecha

    // Consulta SQL utilizando un marcador de posición
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
        HOUR(hora)";  // Aseguramos que las horas se ordenen de menor a mayor

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro de la fecha
        $stmt->bind_param("s", $fecha);  // 's' indica que el parámetro es una cadena

        // Ejecutar la consulta
        $stmt->execute();
        
        // Obtener el resultado
        $result = $stmt->get_result();
        
        // Generar la tabla para la fecha específica
        echo "<h2>Informe de Asistencia para la fecha: $fecha</h2>";
        echo "<div class='tabla-container'>"; # utiliza esta linea 
        echo "<table>
                <thead>
                    <tr>
                        <th>Intervalo</th>
                        <th>Total de Asistencia</th>
                    </tr>
                </thead>
                <tbody>";

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Recorrer los resultados y mostrarlos en la tabla
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['intervalo_hora']) . "</td>
                        <td>" . htmlspecialchars($row['total']) . "</td>
                      </tr>";
            }
        } else {
            // Si no se encontraron resultados, mostrar un mensaje
            echo "<tr><td colspan='3'>No se encontraron resultados para la fecha: $fecha</td></tr>";
        }
        echo "</table></div>";
    }

    // Si no se proporcionó una fecha, mostrar un mensaje de error
}

// Fin de la consulta por fecha

// Si se proporcionó mes y año
if ($mes && $anio) {
    // Consulta SQL para el mes y año
    $sql = "SELECT 
            DATE(fecha) AS fecha,
            COUNT(*) AS total_asistencia
            FROM asistencia
            WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?
            GROUP BY fecha
            ORDER BY fecha;";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros de mes y año
        $stmt->bind_param("ss", $mes,  $anio);  // 'ss' indica que ambos parámetros son cadenas de texto

        // Ejecutar la consulta
        $stmt->execute();
        
        // Obtener el resultado
        $result = $stmt->get_result();
        
        // Generar la tabla para el mes y año
        echo "<h2>Informe de Asistencia para el mes: $mesNombre de $anio</h2>";
        echo "<div class='tabla-container'>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Total de Asistencia</th>
                    </tr>
                </thead>
                <tbody>";

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Recorrer los resultados y mostrarlos en la tabla
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['fecha']) . "</td>
                        <td>" . htmlspecialchars($row['total_asistencia']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No se encontraron resultados para el mes: $mes de $anio</td></tr>";
        }
        echo "</table></div>";
    }
}

// Si se proporcionó año
if ($anios) {
    // Consulta SQL para el año
    $sql = "SELECT 
            YEAR(fecha) AS año, 
            MONTHNAME(fecha) AS mes, 
            SUM(asistencia) AS total_asistencia
        FROM asistencia
        WHERE YEAR(fecha) = ?
        GROUP BY YEAR(fecha), MONTH(fecha)
        ORDER BY MONTH(fecha)";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular el parámetro de año
        $stmt->bind_param("i", $anios); // 'i' indica que el parámetro es un entero

        // Ejecutar la consulta
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->get_result();

        // Generar la tabla para el año
        echo "<h2>Informe de Asistencia para el año: $anios</h2>";
        echo "<div class='tabla-container'>";
        echo "<table>
                <thead>
                    <tr>
                        <th>Mes</th>
                        <th>Total de Asistencia</th>
                    </tr>
                </thead>
                <tbody>";

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['mes']) . "</td>
                        <td>" . htmlspecialchars($row['total_asistencia']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='2'>No se encontraron resultados para el año: $anios</td></tr>";
        }
        echo "</table></div>";
    }
}

if ($fechai && $fechat) {
    $sql = "WITH Intervalos AS (
        SELECT DATE(fecha) AS fecha,
               CONCAT(LPAD(HOUR(hora), 2, '0'), ':00 - ', LPAD((HOUR(hora) + 1) % 24, 2, '0'), ':00') AS intervalo,
               COUNT(*) AS total_asistencia
        FROM asistencia
        WHERE fecha BETWEEN ? AND ?
          AND HOUR(hora) BETWEEN 8 AND 23
        GROUP BY fecha, intervalo
    )
    SELECT fecha, intervalo, total_asistencia
    FROM Intervalos i
    WHERE total_asistencia = (SELECT MAX(total_asistencia) FROM Intervalos WHERE fecha = i.fecha)
    ORDER BY fecha;";

    // Preparar la consulta
    if ($stmt = $conn->prepare($sql)) {
        // Vincular los parámetros de fecha de inicio y fecha de fin
        $stmt->bind_param("ss", $fechai, $fechat); // 'ss' indica que ambos parámetros son cadenas (strings)
    
        // Ejecutar la consulta
        $stmt->execute();
    
        // Obtener el resultado
        $result = $stmt->get_result();
    
        // Generar la tabla para el informe de asistencia
        echo "<h2>Informe de Asistencia para el período: $fechai a $fechat</h2>";
        echo "<div class='tabla-container'>";
        echo "<table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Intervalo</th>
                    <th>Total de Asistencia</th>
                </tr>
            </thead>
            <tbody>";
        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['fecha']) . "</td>
                        <td>" . htmlspecialchars($row['intervalo']) . "</td>
                        <td>" . htmlspecialchars($row['total_asistencia']) . "</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No se encontraron resultados para el período: $fechai a $fechat</td></tr>";
        }
    
        echo "</table></div>";
    
        // Cerrar el statement
        $stmt->close();
    }
}

// Cerrar la conexión a la base de datos
$conn->close();

?>
