<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Asistencia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Agregado Font Awesome -->
    <link rel="stylesheet" href="../css/estilo1.css">
</head>
<style>
    .tabla-container {
        background-color: #3A4B90;
    }
    h1 {
        text-align: center;
    }
</style>
<body>
<header>
    <div class="logo text-center mb-4">
        <img src="../img/logo.png" alt="Logo USM">
    </div>
    <div class="opciones">
        <a href="menuOpciones.html" title="Ir a Inicio" style="margin-left: 5px; font-size: 25px; position: relative; top: 0px;">
            <i class="fas fa-home"></i>
        </a>
    </div>
</header>

<?php
// Conexión a la base de datos
include '../php/conexionBD.php';

// Definir el año y mes dinámicamente (por ejemplo, el mes actual)
$anio = 2024;  // O puedes cambiarlo a cualquier año deseado
$mes = 11;     // Cambia este valor al mes deseado

// Consulta SQL utilizando prepared statements para evitar inyecciones SQL
$sql = "
    WITH total_asistentes_mes AS (
        SELECT COUNT(*) AS total_asistentes  -- Total de todos los asistentes del mes
        FROM salas
        WHERE YEAR(fecha) = ? AND MONTH(fecha) = ?  -- Filtra por mes y año
    ),
    ranked_salas AS (
        SELECT
            DATE(fecha) AS fecha,
            sala,
            COUNT(*) AS cantidad_asistentes,  -- Cuenta los asistentes por sala y fecha
            ROW_NUMBER() OVER (PARTITION BY DATE(fecha) ORDER BY COUNT(*) DESC) AS ranking
        FROM salas
        WHERE YEAR(fecha) = ? AND MONTH(fecha) = ?  -- Filtra por mes y año
        GROUP BY DATE(fecha), sala  -- Agrupa por fecha y sala
    )
    SELECT
        rs.fecha,
        rs.sala,
        rs.cantidad_asistentes,
        ROUND((rs.cantidad_asistentes / tam.total_asistentes) * 100, 2) AS porcentaje_asistencia  -- Calcula el porcentaje sin el símbolo '%'
    FROM ranked_salas rs
    JOIN total_asistentes_mes tam  -- Junta los datos con el total de asistentes del mes
    WHERE rs.ranking = 1  -- Selecciona solo la sala con más asistentes por día
    ORDER BY porcentaje_asistencia DESC, rs.fecha ASC"; 

// Preparar la consulta
$stmt = $conn->prepare($sql);

// Enlazar parámetros (aquí usamos 'i' para enteros, ya que el año y el mes son enteros)
$stmt->bind_param("iiii", $anio, $mes, $anio, $mes); 

// Ejecutar la consulta
$stmt->execute();

// Obtener los resultados
$result = $stmt->get_result();

// Verificar si se obtuvieron resultados
if ($result->num_rows > 0) {
    // Mostrar los resultados en una tabla HTML
    echo "<h1>Informe de asistencia por sala con mayor asistencia para el mes del año</h1>";
    echo "<div class='tabla-container'>";
    echo "<table border='1' class='visitas-table'>
            <tr>
                <th>Fecha</th>
                <th>Sala</th>
                <th>Cantidad de Asistentes</th>
                <th>Porcentaje de Asistencia</th>
            </tr>";

    // Recorrer y mostrar cada fila de resultados
    while ($row = $result->fetch_assoc()) {
        // Asegurarnos de que el porcentaje es un número flotante para su formato
        $porcentaje_asistencia_num = $row['porcentaje_asistencia'];

        // Mostrar los resultados con el porcentaje formateado correctamente
        echo "<tr>
                <td>{$row['fecha']}</td>
                <td>{$row['sala']}</td>
                <td>{$row['cantidad_asistentes']}</td>
                <td>" . number_format($porcentaje_asistencia_num, 2) . "%</td> <!-- Aquí añadimos el símbolo '%' después -->
            </tr>";
    }

    // Cerrar la tabla
    echo "</table></div>";
} else {
    // Mostrar mensaje si no se encontraron visitas
    echo "<h3>No se encontraron visitas para la fecha seleccionada.</h3>";
}

// Cerrar la sentencia y la conexión
$stmt->close();
$conn->close();
?>

</body>
</html>
